<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollementResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $toReturn =  [
            'grade' => $this->pivot->grade,
        ];


        if($request->routeIs('course_sections.show')) {
            $toReturn['student'] = StudentResource::make($this);
        } else if($request->routeIs('students.show')) {
            $toReturn['course_section'] = CourseSectionResource::make($this);
            $toReturn['course'] = CourseResource::make($this->course);
            $toReturn['instructor'] = FacultyResource::make($this->instructor);
        }

        return $toReturn;
    }
}