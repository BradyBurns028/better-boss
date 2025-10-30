<?php

namespace App\Http\Controllers;

use App\Models\Courses\Course;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Filters\CourseFilter;

class CourseController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Includes
        $allowedIncludes = ['department', 'prerequisite', 'dependents', 'sections', 'degreeRequirements', 'plans'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters
        (new CourseFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'course_code', 'name', 'credits', 'department_id', 'created_at'];
        $sort = (string) $request->query('sort', 'course_code');
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'course_code';
        }
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = CourseResource::collection($paginator->items());
        $meta = [
            'page' => $paginator->currentPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];

        return $this->response($data, $meta);
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

        if (isset($data['course_code'])) $course->course_code = $data['course_code'];
        if (isset($data['name'])) $course->name = $data['name'];
        if (array_key_exists('description', $data)) $course->description = $data['description'];
        if (isset($data['credits'])) $course->credits = $data['credits'];
        if (isset($data['department_id'])) $course->department_id = $data['department_id'];
        if (array_key_exists('prerequisite_id', $data)) $course->prerequisite_id = $data['prerequisite_id'];

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
