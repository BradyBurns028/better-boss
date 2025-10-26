<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Enums\PlannedCourseEnum;

class UpdatePlannedCoursePivotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_of_study_id' => 'sometimes|integer|exists:plan_of_studies,id',
            'course_id' => 'sometimes|integer|exists:courses,id',
            'course_section_id' => 'sometimes|nullable|integer|exists:course_sections,id',
            'year' => 'sometimes|nullable|integer|min:1900|max:2100',
            'term' => 'sometimes|nullable|string|max:255',
            'status' => ['sometimes', Rule::enum(PlannedCourseEnum::class)],
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
