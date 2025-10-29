<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $user = $this->route('user'); // The user being updated
        // return auth()->check() && auth()->id() === $user->id;
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? null;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'sometimes|nullable|string',
        ];
    }
}
