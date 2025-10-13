<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;

class AdminController extends AbstractController
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
        ]);

        // Create the user
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Create the admin record linked to the user
        $admin = Admin::create([
            'user_id' => $user->id,
        ]);

        return [
            'user'=>$user,
            'admin'=>$admin
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        $admin->load('user');

        return $this->response(data: AdminResource::make($admin));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $admin->user_id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        $user = $admin->user;

        if (isset($data['first_name'])) $user->first_name = $data['first_name'];
        if (isset($data['last_name']))  $user->last_name = $data['last_name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = bcrypt($data['password']);

        $user->save();

        return $admin;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
