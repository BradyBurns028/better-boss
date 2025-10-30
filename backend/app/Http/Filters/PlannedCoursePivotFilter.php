<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PlannedCoursePivotFilter extends AbstractApiFilter {
    protected array $safeParms = [
        'plan_of_study_id' => ['eq'],
        'course_id'        => ['eq'],
        'course_section_id'=> ['eq'],
        'year'             => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'term'             => ['eq'],
        'status'           => ['eq'],
    ];

    protected array $operatorMap = [
        'eq'  => '=',
        'ne'  => '!=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>=',
    ];

    public function apply(Request $request, Builder $query): Builder {
        foreach ($this->transform($request) as [$column, $operator, $value]) {
            $query->where($column, $operator, $value);
        }

        // explicit null for course_section_id
        if ($request->has('course_section_id')) {
            $val = $request->query('course_section_id');
            if ($val === null || $val === '') {
                $query->whereNull('course_section_id');
            }
        }

        return $query;
    }
}
