<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DegreeProgramFilter extends AbstractApiFilter {
    protected array $safeParms = [
        'department_id' => ['eq'],
        'program_chair' => ['eq'],
        'name'          => ['like'],
    ];

    protected array $operatorMap = [
        'eq'   => '=',
        'ne'   => '!=',
        'like' => 'ilike',
    ];

    public function apply(Request $request, Builder $query): Builder {
        foreach ($this->transform($request) as [$column, $operator, $value]) {
            if ($operator === 'ilike') {
                $query->where($column, 'ilike', "%{$value}%");
            } else {
                $query->where($column, $operator, $value);
            }
        }
        return $query;
    }
}
