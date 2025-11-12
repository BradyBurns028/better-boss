<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DegreeRequirementResource extends JsonResource{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course' => CourseResource::make($this),
            'minimum_grade' => $this->pivot->minimum_grade,
            'course_set' => $this->pivot->course_set,
        ];
    }
}
