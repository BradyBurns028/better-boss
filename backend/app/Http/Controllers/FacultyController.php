<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\FacultyResource;

class FacultyController extends AbstractController
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
            'office' => 'sometimes|nullable|string|max:255',
            'role_type' => 'required|string',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $faculty = Faculty::create([
            'user_id' => $user->id,
            'office' => $data['office'] ?? null,
            'role_type' => $data['role_type'],
            'department_id' => $data['department_id'],
        ]);

        return $this->response([
            'user' => $user,
            'faculty' => $faculty,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        $faculty->load('user', 'department', 'degreePrograms', 'advisees');

        return $this->response(data: FacultyResource::make($faculty));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $faculty->user_id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'office' => 'sometimes|nullable|string|max:255',
            'role_type' => 'sometimes|required|string',
            'department_id' => 'sometimes|required|integer|exists:departments,id',
        ]);

        $user = $faculty->user;

        if (isset($data['first_name'])) $user->first_name = $data['first_name'];
        if (isset($data['last_name'])) $user->last_name = $data['last_name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);

        $user->save();

        if (isset($data['office'])) $faculty->office = $data['office'];
        if (isset($data['role_type'])) $faculty->role_type = $data['role_type'];
        if (isset($data['department_id'])) $faculty->department_id = $data['department_id'];

        $faculty->save();

        return $this->response($faculty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        //
    }
}
