<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
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
        $user = auth()->user();

        // Specific error when there's no authenticated user
        if (!$user) {
            return $this->error(401, 'No authenticated user', 'not_authenticated');
        }

        try {
            $deleted = $user->tokens()->delete();

            // Specific error when user had no tokens to delete
            if ($deleted === 0) {
                return $this->error(400, 'No active tokens found for this user', 'no_tokens');
            }

            return $this->response('Logout successful');

        } catch (Exception $e) {

            return $this->error(500, 'logout_failed', 'logout_failed');
        }
    }
}
