<?php

namespace App\Http\Controllers;

use App\Models\Courses\PlannedCoursePivot;
use Illuminate\Http\Request;
use App\Http\Resources\PlannedCoursePivotResource;
use App\Http\Requests\StorePlannedCoursePivotRequest;
use App\Http\Requests\UpdatePlannedCoursePivotRequest;

class PlannedCoursePivotController extends AbstractController
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
    public function store(StorePlannedCoursePivotRequest $request)
    {
        $data = $request->validated();

        $plannedCoursePivot = PlannedCoursePivot::create([
            'plan_of_study_id' => $data['plan_of_study_id'],
            'course_id' => $data['course_id'],
            'course_section_id' => $data['course_section_id'] ?? null,
            'year' => $data['year'] ?? null,
            'term' => $data['term'] ?? null,
            'status' => $data['status'],
        ]);

        return $this->response($plannedCoursePivot);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlannedCoursePivot $plannedCoursePivot)
    {
        return $this->response(data: PlannedCoursePivotResource::make($plannedCoursePivot));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlannedCoursePivotRequest $request, PlannedCoursePivot $plannedCoursePivot)
    {
        $data = $request->validated();

        $plannedCoursePivot->save();

        return $this->response($plannedCoursePivot);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlannedCoursePivot $plannedCoursePivot)
    {
        //
    }
}
