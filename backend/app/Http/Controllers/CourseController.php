<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Models\Courses\Course;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Filters\CourseFilter;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends AbstractController {
    public function __construct(
        private readonly CourseFilter $filter,
    ){}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {
        /** @var User $user */
        $user = auth()->user();

        if(!$user->can(PermissionEnum::VIEW_COURSES->value)){
            return $this->error(403, 'You do not have permission to view courses.', 'forbidden');
        }

        /** @var Organization $organization */
        $organization = $user->organization;
        $orgId = $organization?->id;

        $query = $orgId
            ? Course::forOrganization($orgId)
            : Course::query();

        $query->with(['department', 'prerequisite', 'dependents', 'sections', 'plans']);

        $this->filter->apply($request, $query);

        $query->orderBy('course_code');
        $perPage = $request->query('per_page', 15);
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
    public function store(StoreCourseRequest $request): Response {
        /** @var User $user */
        $user = auth()->user();

        if(!$user->can(PermissionEnum::CREATE_COURSES->value)){
            return $this->error(403, 'You do not have permission to create new courses.', 'forbidden');
        }

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
    public function show(Course $course): Response {
        /** @var User $user */
        $user = auth()->user();

        if(!$user->can(PermissionEnum::VIEW_COURSES->value)){
            return $this->error(403, 'You do not have permission to view courses.', 'forbidden');
        }

        $course->load([
            'department',
            'prerequisite',
            'dependents',
            'sections',
            'sections.instructor',
            'degreeRequirements',
            'plans'
        ]);

        return $this->response(CourseResource::make($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): Response {
        /** @var User $user */
        $user = auth()->user();

        if(!$user->can(PermissionEnum::EDIT_COURSES->value)){
            return $this->error(403, 'You do not have permission to edit courses.', 'forbidden');
        }

        $data = $request->validated();
        $course->update($data);

        return $this->response($course->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): Response {
        /** @var User $user */
        $user = auth()->user();

        if(!$user->can(PermissionEnum::DELETE_COURSES->value)){
            return $this->error(403, 'You do not have permission to delete courses.', 'forbidden');
        }

        $course->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Course deleted successfully.']);
    }
}
