<?php

namespace App\Http\Controllers;

use App\Models\DegreeProgram;
use Illuminate\Http\Request;
use App\Http\Resources\DegreeProgramResource;
use App\Http\Requests\StoreDegreeProgramRequest;
use App\Http\Requests\UpdateDegreeProgramRequest;

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
    public function store(StoreDegreeProgramRequest $request)
    {
        $data = $request->validated();

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
    public function update(UpdateDegreeProgramRequest $request, DegreeProgram $degreeProgram)
    {
        $data = $request->validated();

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
