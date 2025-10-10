<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class ApiResponse
 *
 * Minimal response builder for your standard API envelope.
 * It centralizes how success and error payloads are shaped and returned as JSON.
 *
 * ## Envelope Shape
 * - **Success** (`2xx`):
 *   ```json
 *   {
 *     "success": true,
 *     "data": {...|[...]},
 *     "meta": {...},               // optional metadata (pagination, etc.)
 *     "info": { "status_code": 200 }
 *   }
 *   ```
 *
 * - **Error** (non-`2xx`):
 *   ```json
 *   {
 *     "success": false,
 *     "info": {
 *       "status_code": 422,
 *       "error": {
 *         "code": "validation_failed",
 *         "message": "The given data was invalid.",
 *         "details": { "email": ["The email has already been taken."] }
 *       }
 *     }
 *   }
 *   ```
 *
 * ## Typical Usage
 * You normally won’t instantiate this directly—use `$this->response()` and `$this->error()` from `AbstractController`.
 *
 * If needed, you can still create responses manually:
 * ```php
 * return ApiResponse::make()
 *   ->withData(['hello' => 'world'])
 *   ->withMeta(['trace_id' => 'abc123'])
 *   ->render();
 * ```
 *
 * @package App\Http\Responses
 */
class ApiResponse {
    /**
     * HTTP status code to send with the response.
     * Defaults to 200; set via {@see withCode()} for errors or custom codes.
     *
     * @var int
     */
    protected int $code = 200;

    /**
     * Machine-readable error code (present only on failure).
     *
     * @var string|null
     */
    protected ?string $error_code = null;

    /**
     * Human-readable error message (present only on failure).
     *
     * @var string|null
     */
    protected ?string $error_message = null;

    /**
     * Optional structured error details (e.g., validation errors).
     *
     * @var array<string,mixed>|null
     */
    protected ?array $error_details = null;

    /**
     * @param bool  $success  Whether the operation succeeded (auto-derived from HTTP status in {@see render()}).
     * @param array $info     Transport info; this builder will populate `status_code` and `error` as needed.
     * @param mixed $data     Main payload. Included only when success = true.
     * @param array $meta     Auxiliary meta. Included only when success = true.
     */
    public function __construct(
       public bool $success = true,
       public array $info = [],
       public mixed $data = [],
       public array $meta = []
    ) {}

    /**
     * Set the `meta` portion of the payload (included only when success = true).
     *
     * @param array<string,mixed> $meta
     * @return $this
     */
    public function withMeta(array $meta = []): self {
        $this->meta = $meta;
        return $this;
    }

    /**
     * Set the `data` portion of the payload (included only when success = true).
     *
     * @param mixed $data
     * @return $this
     */
    public function withData(mixed $data): self {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the HTTP status code to be used in the response.
     *
     * @param int $code
     * @return $this
     *
     * @example
     *  ```php
     *  ApiResponse::make()->withCode(201)->withData($record)->render();
     *  ```
     */
    public function withCode(int $code): self {
        $this->code = $code;
        return $this;
    }

    /**
     * Populate error details for a failed response.
     * Note: Calling this does not change the HTTP status; use {@see withCode()} for that.
     *
     * @param string $error_code     Machine-readable code (e.g., "forbidden", "validation_failed").
     * @param string $error_message  Human-readable message.
     * @param array<string,mixed> $error_details Optional structured data (e.g., validation errors).
     *
     * @return $this
     *
     * @example
     *  ```php
     *  ApiResponse::make()
     *    ->withCode(403)
     *    ->withError('forbidden', 'You do not have permission.')
     *    ->render();
     *  ```
     */
    public function withError(string $error_code, string $error_message, array $error_details = []): self {
        $this->error_code = $error_code;
        $this->error_message = $error_message;
        $this->error_details = $error_details;

        return $this;
    }

    /**
     * Render the standardized JSON response.
     *
     * Behavior:
     * - Sets `success = true` for HTTP 2xx status codes; otherwise false.
     * - Includes `data` and `meta` only on success.
     * - Includes `info.error` only on failure.
     * - Adds `info.status_code` (HTTP status).
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse {
        $this->success = $this->code >= 200 && $this->code < 300;

        $payload = [
            'success' => $this->success,
            'data'    => $this->success ? $this->data : null,
            'meta'    => $this->success ? $this->meta : null,
            'info'    => [
                'status_code' => $this->code,
                'error' => $this->success ? null : [
                    'code'    => $this->error_code,
                    'message' => $this->error_message,
                    'details' => $this->error_details,
                ],
            ],
        ];

        return (new JsonResponse($payload, $this->code));
    }

    /**
     * Fluent constructor.
     *
     * @return static
     */
    public static function make(): ApiResponse {
        return new static();
    }
}