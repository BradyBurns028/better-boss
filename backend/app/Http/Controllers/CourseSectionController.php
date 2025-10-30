<?php

namespace App\Http\Controllers;

use App\Models\Courses\CourseSection;
use Illuminate\Http\Request;
use App\Http\Resources\CourseSectionResource;
use App\Http\Requests\StoreCourseSectionRequest;
use App\Http\Requests\UpdateCourseSectionRequest;

class CourseSectionController extends AbstractController
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
    public function store(StoreCourseSectionRequest $request)
    {
        $data = $request->validated();

        $courseSection = CourseSection::create([
            'course_id' => $data['course_id'],
            'section_number' => $data['section_number'],
            'term' => $data['term'],
            'year' => $data['year'],
            'time' => $data['time'] ?? null,
            'instructor_id' => $data['instructor_id'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'room_number' => $data['room_number'] ?? null,
        ]);

        return $this->response($courseSection);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseSection $courseSection)
    {
        $courseSection->load(['course', 'instructor', 'plans']);

        return $this->response(data: CourseSectionResource::make($courseSection));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseSectionRequest $request, CourseSection $courseSection)
    {
        $data = $request->validated();

        $courseSection->save();

        return $this->response($courseSection);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSection $courseSection)
    {
        //
    }
}
