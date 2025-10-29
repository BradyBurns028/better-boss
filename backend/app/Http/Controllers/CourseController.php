<?php

namespace App\Http\Controllers;

use App\Models\Courses\Course;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends AbstractController
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
    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();

        $course = Course::create([
            'course_code' => $data['course_code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'credits' => $data['credits'],
            'department_id' => $data['department_id'],
            'prerequisite_id' => $data['prerequisite_id'] ?? null,
        ]);

        return $this->response($course);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['department', 'prerequisite', 'dependents', 'sections', 'degreeRequirements', 'plans']);

        return $this->response(data: CourseResource::make($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $data = $request->validated();

        $course->save();

        return $this->response($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
