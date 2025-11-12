<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'current_classes'=>$this->resource->classes,
            'organization' => $this->resource->organization,
            'advisor' => FacultyResource::make($this->resource->faculty),
            'degree_program'=>$this->whenLoaded('degreeProgram'),
            'user'=>UserResource::make($this->whenLoaded('user')),
            'enrollments' => EnrollementResource::collection($this->whenLoaded('enrollments')),
        ];
    }
}
