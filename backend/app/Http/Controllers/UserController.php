<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Enums\UserType;
use App\Models\Student;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;

class UserController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? null,
        ]);

        $user->load(['admins', 'organizations', 'students', 'faculties']);

        return $this->response(data: UserResource::make($user));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['admins', 'organizations', 'students', 'faculties']);

        return $this->response(data: UserResource::make($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Handle password hashing if present
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Mass assign allowed fields
        $user->fill($data);
        $user->save();

        $user = $user->fresh(['admins', 'organizations', 'students', 'faculties']);

        return $this->response(data: UserResource::make($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
