<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'degree_program' => 'required|integer|exists:degree_programs,id',
            'faculty_id' => 'nullable|integer|exists:faculties,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        // accept camelCase keys
        $normalized = [];
        foreach ($this->all() as $key => $value) {
            $snake = Str::snake($key);
            if ($snake !== $key) $normalized[$snake] = $value;
        }
        if (!empty($normalized)) $this->merge($normalized);
    }
}
