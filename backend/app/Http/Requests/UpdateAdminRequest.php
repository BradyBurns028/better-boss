<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $user = $this->route('user'); // The user being updated
        // return auth()->check() && auth()->id() === $user->id;
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email',
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }
}
