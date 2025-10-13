<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Resources\UserResource;
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'sometimes|nullable|string',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? null,
        ]);


        return $this->response($user);
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
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'user_type' => 'sometimes|nullable|string',
        ]);

        if (isset($data['first_name'])) $user->first_name = $data['first_name'];
        if (isset($data['last_name'])) $user->last_name = $data['last_name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = bcrypt($data['password']);
        if (array_key_exists('user_type', $data)) $user->user_type = $data['user_type'];

        $user->save();

        return $this->response($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
