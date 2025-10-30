<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_code' => 'required|string|max:255|unique:courses,course_code',
            'name' => 'required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'credits' => 'required|integer|min:0',
            'department_id' => 'required|integer|exists:departments,id',
            'prerequisite_id' => 'sometimes|nullable|integer|exists:courses,id',
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
