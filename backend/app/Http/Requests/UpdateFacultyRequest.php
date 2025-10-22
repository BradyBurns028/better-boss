<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $user = $this->route('user'); // The user being updated
        // return auth()->check() && auth()->id() === $user->id;
        return true;
    }

    public function rules(): array
    {
        $facultyId = $this->route('faculty')?->id ?? null;

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email',
            'password' => 'sometimes|string|min:8|confirmed',
            'office' => 'sometimes|nullable|string|max:255',
            'role_type' => 'sometimes|string',
            'department_id' => 'sometimes|integer|exists:departments,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];
        foreach ($this->all() as $key => $value) {
            $snake = Str::snake($key);
            if ($snake !== $key) $normalized[$snake] = $value;
        }
        if (!empty($normalized)) $this->merge($normalized);
    }
}
