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
 * - search=... (matches normalized course_code like CSC-130 == csc130 and also name ILIKE) <-CURRENTLY NOT WORKING
 * - term, year, instructor_id (applied through related sections)
 */
class CourseFilter extends AbstractApiFilter {
    /** @var array<string, list<string>> */
    protected array $safeParms = [
        'department_id'   => ['eq'],
        'prerequisite_id' => ['eq'],
        'credits'         => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'name'            => ['like'],
    ];

    /** @var array<string,string> */
    protected array $operatorMap = [
        // inherit from parent and add case-insensitive like for Postgres
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
        // Standard operators via parent transform
        foreach ($this->transform($request) as [$column, $operator, $value]) {
            if ($operator === 'ilike') {
                $query->where($column, 'ilike', "%{$value}%");
                continue;
            }
            $query->where($column, $operator, $value);
        }

        // Handle nullable prerequisite_id (explicit empty string -> IS NULL)
        if ($request->has('prerequisite_id')) {
            $val = $request->query('prerequisite_id');
            if ($val === null || $val === '') {
                $query->whereNull('prerequisite_id');
            }
        }

        // Unified search across normalized course_code and name
        if ($request->filled('search')) {
            $search = (string) $request->query('search');
            $normalized = $this->normalizeCourseCode($search);

            $query->where(function (Builder $q) use ($normalized, $search) {
                // Match normalized course_code using Postgres REGEXP_REPLACE
                $q->whereRaw(
                    "LOWER(regexp_replace(course_code, '[^a-z0-9]', '', 'g')) LIKE ?",
                    ["%{$normalized}%"]
                )
                // Or name ILIKE
                ->orWhere('name', 'ilike', "%{$search}%");
            });
        }

        // Section-level filters via whereHas
        if ($request->filled('term') || $request->filled('year') || $request->filled('instructor_id')) {
            $query->whereHas('sections', function (Builder $s) use ($request) {
                if ($request->filled('term')) {
                    $s->where('term', (string) $request->query('term'));
                }
                if ($request->filled('year')) {
                    $s->where('year', (int) $request->query('year'));
                }
                if ($request->filled('instructor_id')) {
                    $s->where('instructor_id', (int) $request->query('instructor_id'));
                }
            });
        }

        return $query;
    }

    /**
     * Normalize course code input: remove non-alphanumerics and lowercase.
     */
    private function normalizeCourseCode(string $code): string {
        $clean = preg_replace('/[^a-z0-9]/i', '', $code) ?? $code;
        return strtolower($clean);
    }
}
