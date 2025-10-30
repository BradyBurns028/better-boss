<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrganizationFilter extends AbstractApiFilter {
    protected array $safeParms = [
        'admin_id' => ['eq'],
        'owner_id' => ['eq'],
        'name'     => ['like'],
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

        // owner_id nullable handling if explicitly empty
        if ($request->has('owner_id')) {
            $val = $request->query('owner_id');
            if ($val === null || $val === '') {
                $query->whereNull('owner_id');
            }
        }

        return $query;
    }
}
