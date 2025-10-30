<?php

namespace App\Http\Controllers;

use App\Models\Courses\CourseSection;
use Illuminate\Http\Request;
use App\Http\Resources\CourseSectionResource;
use App\Http\Requests\StoreCourseSectionRequest;
use App\Http\Requests\UpdateCourseSectionRequest;
use App\Http\Filters\CourseSectionFilter;

class CourseSectionController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseSection::query();

        // Includes
        $allowedIncludes = ['course', 'instructor', 'plans'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters via CourseSectionFilter
        (new CourseSectionFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'course_id', 'section_number', 'term', 'year', 'created_at'];
        $sort = (string) $request->query('sort', 'year');
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'year';
        }
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = CourseSectionResource::collection($paginator->items());
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

        if (isset($data['course_id'])) $courseSection->course_id = $data['course_id'];
        if (isset($data['section_number'])) $courseSection->section_number = $data['section_number'];
        if (isset($data['term'])) $courseSection->term = $data['term'];
        if (isset($data['year'])) $courseSection->year = $data['year'];
        if (array_key_exists('time', $data)) $courseSection->time = $data['time'];
        if (array_key_exists('instructor_id', $data)) $courseSection->instructor_id = $data['instructor_id'];
        if (array_key_exists('capacity', $data)) $courseSection->capacity = $data['capacity'];
        if (array_key_exists('room_number', $data)) $courseSection->room_number = $data['room_number'];

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
