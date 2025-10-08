<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractController
 *
 * Base controller providing two helpers—{@see response()} and {@see error()}—to standardize
 * API responses across your application. These helpers emit a JSON envelope with the
 * structure:
 *
 *  {
 *    "success": bool,
 *    "data": mixed,
 *    "meta": object,
 *    "info": {
 *      "status_code": int,
 *      "error": {
 *        "code": string|null,
 *        "message": string|null,
 *        "details": array|null
 *      }
 *    }
 *  }
 *
 * ## Usage
 * - Return successful data:
 *   ```php
 *   return $this->response(['user' => $user]);
 *   ```
 *
 * - Return paginated data (auto-extracts items + builds meta):
 *   ```php
 *   $p = User::query()->paginate(25);
 *   return $this->response($p);
 *   ```
 *
 * - Return an error:
 *   ```php
 *   return $this->error(403, 'Forbidden', 'forbidden');
 *   ```
 *
 * @package App\Http\Controllers
 */
abstract class AbstractController {

    /**
     * Build and return a **successful** API response.
     *
     * - If `$data` is a {@see LengthAwarePaginator}, this method will:
     *   1) Move `$data->items()` into the `data` field
     *   2) Populate `meta` with pagination keys: page, total, last_page, per_page, current_page
     *
     * - Otherwise, `$data` will be sent as-is and `$meta` is included verbatim.
     *
     * @param mixed $data
     *     Arbitrary response payload or an instance of {@see LengthAwarePaginator}.
     *
     * @param array<string,mixed> $meta
     *     Extra metadata to include when `$data` is not a paginator. Ignored when `$data` is a paginator.
     *
     * @param string|ApiResponse $response
     *     A response instance or a **class-string** for the response builder.
     *     Typically left as default: `ApiResponse::class`.
     *
     * @return Response
     *     JSON response with HTTP status code 200 (unless the custom builder changes it).
     *
     * @example Success with plain data
     *  ```php
     *  return $this->response(['ok' => true]);
     *  ```
     *
     * @example Success with pagination
     *  ```php
     *  $p = Post::paginate(15);
     *  return $this->response($p);
     *  ```
     */
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

    /**
     * Build and return a **failed** API response (non-2xx).
     *
     * This helper standardizes error envelopes (validation failures, auth/permission errors,
     * not-found, etc.). It sets the HTTP status code and places error details under
     * `info.error`.
     *
     * @param int $status
     *     The HTTP status code to return (e.g., 400, 401, 403, 404, 422, 500).
     *
     * @param string $message
     *     Human-readable error description (e.g., "The given data was invalid.").
     *
     * @param string $errorCode
     *     Machine-readable error code (e.g., "validation_failed", "forbidden", "not_found").
     *
     * @param array<string,mixed> $details
     *     Optional structured details (e.g., validation errors with field => messages).
     *
     * @return Response
     *     JSON response with the given HTTP status code.
     *
     * @example Validation error (422)
     *  ```php
     *  return $this->error(422, 'The given data was invalid.', 'validation_failed', $validator->errors()->toArray());
     *  ```
     *
     * @example Forbidden (403)
     *  ```php
     *  return $this->error(403, 'Forbidden', 'forbidden');
     *  ```
     */
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