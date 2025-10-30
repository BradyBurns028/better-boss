<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

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
    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'degree_program' => $data['degree_program'],
            'faculty_id' => $data['faculty_id'] ?? null,
        ]);

        return $this->response([
            'user'=>$user,
            'student'=>$student
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): Response {
        $student->load('user', 'faculty', 'degreeProgram', 'degreeProgram.department');

        return $this->response(StudentResource::make($student));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        $user = $student->user;

        if (isset($data['first_name'])) $user->first_name = $data['first_name'];
        if (isset($data['last_name'])) $user->last_name = $data['last_name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);

        $user->save();

        if (isset($data['degree_program'])) $student->degree_program = $data['degree_program'];
        if (array_key_exists('faculty_id', $data)) $student->faculty_id = $data['faculty_id'];

        $student->save();

        return $this->response($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
