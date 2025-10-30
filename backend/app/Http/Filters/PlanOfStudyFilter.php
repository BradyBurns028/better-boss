<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PlanOfStudyFilter extends AbstractApiFilter {
    protected array $safeParms = [
        'degree_program_id' => ['eq'],
        'student_id'        => ['eq'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
    ];

    public function apply(Request $request, Builder $query): Builder {
        foreach ($this->transform($request) as [$column, $operator, $value]) {
            $query->where($column, $operator, $value);
        }
        return $query;
    }
}
