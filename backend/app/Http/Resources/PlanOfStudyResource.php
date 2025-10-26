<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanOfStudyResource extends JsonResource
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
            'degree_program_id' => $this->degree_program_id,
            'student_id' => $this->student_id,

            'degreeProgram' => DegreeProgramResource::make($this->whenLoaded('degreeProgram')),
            'student' => StudentResource::make($this->whenLoaded('student')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'sections' => CourseSectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
