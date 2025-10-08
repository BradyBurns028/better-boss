<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController {
    protected function response(
        mixed $data = [],
        array $meta = [],
        string|ApiResponse $response = ApiResponse::class
    ): Response {
        if ($data instanceof LengthAwarePaginator) {
            $meta = [
                'page' => $data->currentPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
            ];

            $data = $data->items();
        }

        $response = $response::make()->withData($data)->withMeta($meta);

        return $response->render();
    }

    protected function error(
        int $status,
        string $message,
        string $errorCode,
        array $details = [],
    ): Response {
        $response = ApiResponse::make()->withCode($status)->withError($errorCode, $message, $details);
        return $response->render();
    }
}