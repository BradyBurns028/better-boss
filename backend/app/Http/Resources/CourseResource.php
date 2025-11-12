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
        // Resolve pivot source:
        // 1) If we're rendering a course nested under PlanOfStudy->courses,
        //    $this->pivot is set.
        // 2) If we're rendering a top-level course with `plans` relation loaded
        //    (filtered to the current student in the controller), grab the first plan's pivot.
        $planPivot = null;

        if ($this->relationLoaded('plans') && $this->plans && $this->plans->isNotEmpty()) {
            $planPivot = $this->plans->first()->pivot; // pivot from the student's plan
        } elseif (isset($this->pivot)) {
            $planPivot = $this->pivot; // pivot from PlanOfStudy->courses context
        }

        $plan = $this->when(
            !is_null($planPivot),
            fn () => (object) [
                'year'              => $planPivot->year ?? null,
                'term'              => $planPivot->term ?? null,
                'status'            => $planPivot->status ?? null,
                'course_section_id' => $planPivot->course_section_id ?? null,
                // optionally expose composite keys if useful on the client:
                'plan_of_study_id'  => $planPivot->plan_of_study_id ?? null,
                'course_id'         => $planPivot->course_id ?? null,
            ]
        );

        return [
            'id'          => $this->id,
            'course_code' => $this->course_code,
            'name'        => $this->name,
            'description' => $this->description,
            'credits'     => $this->credits,
            'department'  => DepartmentResource::make($this->whenLoaded('department')),
            'prerequisite'=> CourseResource::make($this->whenLoaded('prerequisite')),
            'dependents'  => CourseResource::collection($this->whenLoaded('dependents')),
            'sections'    => CourseSectionResource::collection($this->whenLoaded('sections')),
            'plan'        => $plan,
        ];
    }
}
