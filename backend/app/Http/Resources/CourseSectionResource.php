<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionResource extends JsonResource
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
            'course_id' => $this->course_id,
            'section_number' => $this->section_number,
            'term' => $this->term,
            'year' => $this->year,
            'time' => $this->time,
            'instructor_id' => $this->instructor_id,
            'capacity' => $this->capacity,
            'room_number' => $this->room_number,

            'course' => CourseResource::make($this->whenLoaded('course')),
            'instructor' => FacultyResource::make($this->whenLoaded('instructor')),
            'plans' => PlanOfStudyResource::collection($this->whenLoaded('plans')),
        ];
    }
}
