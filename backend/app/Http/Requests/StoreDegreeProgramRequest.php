<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreDegreeProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:departments,id',
            'program_chair' => 'sometimes|nullable|integer|exists:faculties,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];
        foreach ($this->all() as $key => $value) {
            $snake = Str::snake($key);
            if ($snake !== $key) $normalized[$snake] = $value;
        }
        if (!empty($normalized)) $this->merge($normalized);
    }
}
