<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filters for CourseSection listing.
 *
 * Supported params:
 * - course_id (eq)
 * - section_number (eq)
 * - term (eq)
 * - year (eq, lt, lte, gt, gte)
 * - instructor_id (eq)
 */
class CourseSectionFilter extends AbstractApiFilter {
    /** @var array<string, list<string>> */
    protected array $safeParms = [
        'course_id'      => ['eq'],
        'section_number' => ['eq'],
        'term'           => ['eq'],
        'year'           => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'instructor_id'  => ['eq'],
    ];

    /** @var array<string,string> */
    protected array $operatorMap = [
        'eq'  => '=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>=',
        'ne'  => '!=',
    ];

    public function apply(Request $request, Builder $query): Builder {
        foreach ($this->transform($request) as [$column, $operator, $value]) {
            $query->where($column, $operator, $value);
        }
        return $query;
    }
}
