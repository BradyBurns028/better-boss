<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * CourseFilter applies common filtering to the Course query based on request params.
 *
 * Supported query params:
 * - department_id (eq)
 * - prerequisite_id (eq, nullable supported via prerequisite_id=)
 * - credits (eq, lt, lte, gt, gte)
 * - name[like]=... (case-insensitive)
 * - matches=... (matches normalized course_code like CSC-130 == csc130 and also name ILIKE)
 * - term, year, instructor_id (applied through related sections)
 */
class CourseFilter extends AbstractApiFilter {
    /** @var array<string, list<string>> */
    protected array $safeParms = [
        'department_id' => ['eq'],
        'prerequisite_id' => ['eq'],
        'credits' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'name' => ['eq', 'like'],
        'course_code' => ['eq', 'like'],
        'matches' => ['like'],

        'term' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'year' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'instructor_id' => ['eq'],
    ];

    /** @var array<string,string> */
    protected array $operatorMap = [
        'eq'  => '=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>=',
        'ne'  => '!=',
        'like'=> 'ilike',
    ];

    /**
     * Apply filters to a Course builder from a Request.
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    public function apply(Request $request, Builder $query): Builder {
        $sectionKeys = ['term','year','instructor_id'];
        $pairs = $this->transform($request);
        $coursePairs = [];
        $sectionPairs = [];

        foreach ($pairs as [$column, $operator, $value]) {
            if ($column === 'matches') {
                continue;
            }
            if (\in_array($column, $sectionKeys, true)) {
                $sectionPairs[] = [$column, $operator, $value];
            } else {
                $coursePairs[] = [$column, $operator, $value];
            }
        }

        foreach ($coursePairs as [$column, $operator, $value]) {
            if ($operator === 'ilike') {
                $query->where($column, 'ilike', "%{$value}%");
            } else {
                $query->where($column, $operator, $value);
            }
        }

        // Handle nullable prerequisite_id (explicit empty string -> IS NULL)
        if ($request->has('prerequisite_id')) {
            $val = $request->query('prerequisite_id');
            if ($val === null || $val === '') {
                $query->whereNull('prerequisite_id');
            }
        }

        $matchesLike = data_get($request->query(), 'matches.like');
        if (is_string($matchesLike) && $matchesLike !== '') {
            $needle = $matchesLike;
            $normalized = $this->normalizeCourseCode($needle);
            $needleILike = "%{$needle}%";
            $normalizedLike = "%{$normalized}%";

            $query->where(function (Builder $q) use ($normalizedLike, $needleILike) {
                // normalize: strip non-alphanumerics and lowercase; also handle NULLs
                $q->whereRaw(
                    "LOWER(regexp_replace(COALESCE(course_code, ''), '[^a-z0-9]', '', 'g')) LIKE ?",
                    [$normalizedLike]
                )->orWhere('course_code', 'ilike', $needleILike)
                    ->orWhere('name', 'ilike', $needleILike)
                    ->orWhere('description', 'ilike', $needleILike);;
            });
        }

        // 5) Section-level filters -> only return courses that have matching sections
        if (!empty($sectionPairs)) {
            $query->whereHas('sections', function (Builder $s) use ($sectionPairs) {
                foreach ($sectionPairs as [$column, $operator, $value]) {
                    // Casts for common types
                    if ($column === 'year' || $column === 'instructor_id') {
                        $value = (int) $value;
                    }
                    $s->where($column, $operator, $value);
                }
            });
        }

        return $query;
    }

    /**
     * Normalize course code input: remove non-alphanumerics and lowercase.
     *
     * @param string $code
     * @return string
     */
    private function normalizeCourseCode(string $code): string {
        $clean = preg_replace('/[^a-z0-9]/i', '', $code) ?? $code;
        return strtolower($clean);
    }
}
