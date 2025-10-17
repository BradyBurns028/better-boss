<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;

/**
 * Converts request query params (e.g., ?field[op]=value)
 * into Eloquent where triplets: [column, operator, value].
 */
abstract class AbstractApiFilter {
    /** @var array<string, list<string>> Allowed params and operators. */
    protected array $safeParms = [];

    /** @var array<string, string> Map request param => DB column. */
    protected array $columnMap = [];

    /** @var array<string, string> Map short ops to SQL ops. */
    protected array $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!='
    ];

    /**
     * Transform request query parameters into Eloquent 'where' triplets.
     *
     * @param Request $request Incoming HTTP request carrying query filters.
     * @return array<int, array{0:string,1:string,2:mixed}> List of [column, operator, value] triplets.
    */
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
