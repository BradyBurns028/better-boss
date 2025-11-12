<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'name' => $this->name,
            'degree_programs' => DegreeProgramResource::collection($this->whenLoaded('degreePrograms')),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'department_chair' => FacultyResource::make($this->whenLoaded('departmentChair')),
            'faculties' => FacultyResource::collection($this->whenLoaded('faculty') ?? collect()),
        ];
    }
}
