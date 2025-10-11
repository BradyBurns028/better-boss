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
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'user_type' => $this->whenNotNull($this->user_type),

            // Relations
            'admins' => $this->whenLoaded('admins'),
            'organizations' => $this->whenLoaded('organizations'),
            'students' => $this->whenLoaded('students'),
            'faculties' => $this->whenLoaded('faculties'),
            'roles' => $this->whenLoaded('roles'),
        ];
    }
}
