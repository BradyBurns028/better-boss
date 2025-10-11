<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;

class StudentController extends AbstractController
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
        $fields = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'degree_program' => 'required|integer|exists:degree_programs,id',
            'faculty_id' => 'nullable|integer|exists:faculties,id',
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'degree_program' => $fields['degree_program'],
            'faculty_id' => $fields['faculty_id'] ?? null,
        ]);

        return [
            'user'=>$user,
            'student'=>$student
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return $this->response(StudentResource::make(
            $student->with('user','organization','advisor','degreeProgram')
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $student->user_id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'degree_program' => 'sometimes|required|integer|exists:degree_programs,id',
            'faculty_id' => 'sometimes|nullable|integer|exists:faculties,id',
        ]);

        $user = $student->user;

        if (isset($data['first_name'])) $user->first_name = $data['first_name'];
        if (isset($data['last_name'])) $user->last_name = $data['last_name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);

        $user->save();

        if (isset($data['degree_program'])) $student->degree_program = $data['degree_program'];
        if (array_key_exists('faculty_id', $data)) $student->faculty_id = $data['faculty_id'];

        $student->save();

        return response()->json($student->refresh()->load(['user', 'degreeProgram', 'faculty']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
