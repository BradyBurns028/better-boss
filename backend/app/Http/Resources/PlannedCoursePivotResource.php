<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlannedCoursePivotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'plan_of_study_id' => $this->plan_of_study_id,
            'course_id' => $this->course_id,
            'course_section_id' => $this->course_section_id,
            'year' => $this->year,
            'term' => $this->term,
            'status' => $this->status,
        ];
    }
}
