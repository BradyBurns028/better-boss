<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $organizationModel = null;

        $type = $this->user_type?->value ?? null;

        if ($type === 'student') {
            if ($this->relationLoaded('students') && $this->students) {
                $organizationModel = $this->students->organization;
            }
        } elseif ($type === 'faculty') {
            if ($this->relationLoaded('faculties') && $this->faculties) {
                $organizationModel = $this->faculties->organization;
            }
        } else {
            if (!$organizationModel && $this->relationLoaded('students') && $this->students) {
                $organizationModel = $this->students->organization;
            }
            if (!$organizationModel && $this->relationLoaded('faculties') && $this->faculties) {
                $organizationModel = $this->faculties->organization;
            }
        }

        return [
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'user_type'  => $this->user_type,

            'organization' => $organizationModel
                ? OrganizationResource::make($organizationModel)
                : null,

            'admin'   => AdminResource::make($this->whenLoaded('admins')),
            'student' => StudentResource::make($this->whenLoaded('students')),
            'faculty' => FacultyResource::make($this->whenLoaded('faculties')),
        ];
    }
}
