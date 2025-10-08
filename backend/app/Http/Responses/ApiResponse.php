<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse {
    protected int $code = 200;
    protected ?string $error_code = null;
    protected ?string $error_message = null;
    protected ?array $error_details = null;

    public function __construct(
       public bool $success = true,
       public array $info = [],
       public mixed $data = [],
       public array $meta = []
    ) {}

    public function withMeta(array $meta = []): self {
        $this->meta = $meta;
        return $this;
    }

    public function withData(mixed $data): self {
        $this->data = $data;
        return $this;
    }

    public function withCode(int $code): self {
        $this->code = $code;
        return $this;
    }

    public function withError(string $error_code, string $error_message, array $error_details = []): self {
        $this->error_code = $error_code;
        $this->error_message = $error_message;
        $this->error_details = $error_details;

        return $this;
    }

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

        return (new JsonResponse($payload, $this->code))
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public static function make(): ApiResponse {
        return new static();
    }
}