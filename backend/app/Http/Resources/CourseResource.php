<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_code' => $this->course_code,
            'name' => $this->name,
            'description' => $this->description,
            'credits' => $this->credits,
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'prerequisite' => CourseResource::make($this->whenLoaded('prerequisite')),
            'dependents' => CourseResource::collection($this->whenLoaded('dependents')),
            'sections' => CourseSectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
