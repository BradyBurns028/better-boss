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
            'user'=>UserResource::make($this->whenLoaded('user')),
            'current_classes'=>$this->whenLoaded('classes'),
            'organization'=>$this->whenLoaded('organization'),
            'advisor'=>$this->whenLoaded('advisor'),
            'department'=>[
                'name'=>$this->whenLoaded('degreeProgram.department'),
                'degree_program'=>$this->whenLoaded('degreeProgram.name')
            ]
        ];
    }
}
