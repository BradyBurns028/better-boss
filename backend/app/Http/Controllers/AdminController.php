<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;

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
    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();

        // Create the user
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            // ensure user_type is set (DB requires NOT NULL)
            'user_type' => $data['user_type'] ?? 'admin',
        ]);

        // Create the admin record linked to the user
        $admin = Admin::create([
            'user_id' => $user->id,
        ]);

        return $this->response([
            'user'=>$user,
            'admin'=>$admin
        ]);
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
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $data = $request->validated();

        $user = $admin->user;

        if (isset($data['first_name'])) $user->first_name = $data['first_name'];
        if (isset($data['last_name']))  $user->last_name = $data['last_name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = bcrypt($data['password']);

        $user->save();

        return $this->response($admin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
