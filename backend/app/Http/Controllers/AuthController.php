<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AbstractController {
    public function register(Request $request): Response
    {
        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'user_type' => ['required'],
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'user_type' => $fields['user_type'],
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return $this->response($response);
    }

    public function login(Request $request): Response {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return $this->error(401, 'Login failed', 'validation_failed');
        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return $this->response($response);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function me(Request $request): Response {
        /** @var User $user */
        $user = $request->user('sanctum');

        if (!$user) {
            return $this->error(401, 'No user found', 'no_token');
        }

        if ($user->user_type == UserType::STUDENT) {
            $user->load('students');
        }

        return $this->response(UserResource::make($user));
    }

    public function logout(): Response {
        auth()->user()->tokens()->delete();

        return $this->response('Logout successful');
    }
}
