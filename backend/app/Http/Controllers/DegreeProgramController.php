<?php

namespace App\Http\Controllers;

use App\Models\DegreeProgram;
use Illuminate\Http\Request;
use App\Http\Resources\DegreeProgramResource;

class DegreeProgramController extends AbstractController
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
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:departments,id',
            'program_chair' => 'sometimes|nullable|integer|exists:faculties,id',
        ]);

        $degreeProgram = DegreeProgram::create([
            'name' => $data['name'],
            'department_id' => $data['department_id'],
            'program_chair' => $data['program_chair'] ?? null,
        ]);

        return $this->response($degreeProgram);
    }

    /**
     * Display the specified resource.
     */
    public function show(DegreeProgram $degreeProgram)
    {
        $degreeProgram->load(['department', 'programChair', 'students']);

        return $this->response(data: DegreeProgramResource::make($degreeProgram));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DegreeProgram $degreeProgram)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'department_id' => 'sometimes|required|integer|exists:departments,id',
            'program_chair' => 'sometimes|nullable|integer|exists:faculties,id',
        ]);

        if (isset($data['name'])) $degreeProgram->name = $data['name'];
        if (isset($data['department_id'])) $degreeProgram->department_id = $data['department_id'];
        if (array_key_exists('program_chair', $data)) $degreeProgram->program_chair = $data['program_chair'];

        $degreeProgram->save();

        return $this->response($degreeProgram);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DegreeProgram $degreeProgram)
    {
        //
    }
}
