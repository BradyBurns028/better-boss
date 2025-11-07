<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
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
            'office' => $this->office,
            'role_type' => $this->role_type,

            'degree_programs' => DegreeProgramResource::collection($this->whenLoaded('degreePrograms')),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'user' => UserResource::make($this->resource->user),
            'advisees' => StudentResource::collection($this->whenLoaded('advisees') ?? collect()),
        ];
    }
}
