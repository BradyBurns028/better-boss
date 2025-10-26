<?php

namespace App\Http\Controllers;

use App\Models\Courses\PlanOfStudy;
use Illuminate\Http\Request;
use App\Http\Resources\PlanOfStudyResource;
use App\Http\Requests\StorePlanOfStudyRequest;
use App\Http\Requests\UpdatePlanOfStudyRequest;

class PlanOfStudyController extends AbstractController
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
    public function store(StorePlanOfStudyRequest $request)
    {
        $data = $request->validated();

        $planOfStudy = PlanOfStudy::create([
            'degree_program_id' => $data['degree_program_id'],
            'student_id' => $data['student_id'],
        ]);

        return $this->response($planOfStudy);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanOfStudy $planOfStudy)
    {
        $planOfStudy->load(['degreeProgram', 'student', 'courses', 'sections']);

        return $this->response(data: PlanOfStudyResource::make($planOfStudy));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanOfStudyRequest $request, PlanOfStudy $planOfStudy)
    {
        $data = $request->validated();

        if (isset($data['degree_program_id'])) $planOfStudy->degree_program_id = $data['degree_program_id'];
        if (isset($data['student_id'])) $planOfStudy->student_id = $data['student_id'];

        $planOfStudy->save();

        return $this->response($planOfStudy);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanOfStudy $planOfStudy)
    {
        //
    }
}
