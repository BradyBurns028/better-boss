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
            'current_classes'=>$this->classes,
            'organization'=>$this->organization,
            'advisor'=>$this->advisor,
            'department'=>[
                'name'=>$this->whenLoaded('degreeProgram.department'),
                'degree_program'=>$this->whenLoaded('degreeProgram.name'),
            ],
            
            'user'=>UserResource::make($this->whenLoaded('user'))
        ];
    }
}
