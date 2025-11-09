<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Responses\ApiResponse;
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
        if(!auth()->user()->can(PermissionEnum::VIEW_COURSE_SECTIONS->value)) {
            return $this->error(403, 'You do not have permission to view course sections.', 'forbidden');
        }

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
        if(!auth()->user()->can(PermissionEnum::CREATE_COURSE_SECTIONS->value)) {
            return $this->error(403, 'You do not have permission to create course sections.', 'forbidden');
        }

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
        $user=auth()->user();

        if(!$user->can(PermissionEnum::VIEW_COURSE_SECTIONS->value)) {
            return $this->error(403, 'You do not have permission to view course sections.', 'forbidden');
        }

        $courseSection->load(['course', 'instructor', 'plans']);

        if($user->can(PermissionEnum::VIEW_ADVISEES->value) && $user->faculty_id === $courseSection->instructor->faculty_id) {
            $courseSection->load(['students']);
        }

        return $this->response(CourseSectionResource::make($courseSection));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseSectionRequest $request, CourseSection $courseSection)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_COURSE_SECTIONS->value)) {
            return $this->error(403, 'You do not have permission to edit course sections.', 'forbidden');
        }

        $data = $request->validated();

        $courseSection->save();

        return $this->response($courseSection);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSection $courseSection)
    {
        if(!auth()->user()->can(PermissionEnum::DELETE_COURSE_SECTIONS->value)) {
            return $this->error(403, 'You do not have permission to delete course sections.', 'forbidden');
        }

        $courseSection->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Course section deleted successfully.']);
    }
}
