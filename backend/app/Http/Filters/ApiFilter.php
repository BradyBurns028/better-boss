<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;

abstract class ApiFilter {
    protected array $safeParms = [];

    protected array $columnMap = [];

    protected array $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!='
    ];

    public function transform(Request $request): array {
        $eloQuery = [];

        foreach ($request->query() as $param => $conditions) {
            $column = $this->columnMap[$param] ?? $param;

            if (!is_array($conditions)) continue;

            foreach ($conditions as $operator => $value) {
                if (isset($this->operatorMap[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $value];
                }
            }
        }

        return $eloQuery;
    }
}
