<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DegreeProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->whenLoaded('name'),

            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'program_chair' => FacultyResource::make($this->whenLoaded('programChair')),
            'students' => StudentResource::collection($this->students ?? collect()),
        ];
    }
}
