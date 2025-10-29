<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCourseSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'required|integer|exists:courses,id',
            'section_number' => 'required|integer',
            'term' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'time' => 'sometimes|nullable|date_format:H:i:s',
            'instructor_id' => 'sometimes|nullable|integer|exists:faculties,id',
            'capacity' => 'sometimes|nullable|integer|min:0',
            'room_number' => 'sometimes|nullable|string|max:255',
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
