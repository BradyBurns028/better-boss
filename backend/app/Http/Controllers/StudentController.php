<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Responses\ApiResponse;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Traits\CheckSelfAccess;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Filters\StudentFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class StudentController extends AbstractController
{
    use CheckSelfAccess;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::query();

        if(auth()->user()->can(PermissionEnum::VIEW_ADVISEES->value)) {
            if (is_null(auth()->user()->faculties?->id)) {
                return $this->error(404, 'Faculty ID not found.', 'forbidden');
            }
            $query->where('faculty_id', auth()->user()->faculties?->id);
        } else if(!auth()->user()->can(PermissionEnum::VIEW_STUDENTS->value)) {
            return $this->error(403, 'You do not have permission to view students.', 'forbidden');
        }

        // Includes (support nested degreeProgram.department)
        $allowedIncludes = ['user', 'faculty', 'degreeProgram', 'degreeProgram.department'];
        $query->with($allowedIncludes);

        // Filters
        (new StudentFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'degree_program', 'faculty_id', 'created_at'];
        $sort = (string) $request->query('sort', 'id');
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = StudentResource::collection($paginator->items());
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
    public function store(StoreStudentRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_STUDENTS->value)) {
            return $this->error(403, 'You do not have permission to create students.', 'forbidden');
        }

        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'degree_program' => $data['degree_program'],
            'faculty_id' => $data['faculty_id'] ?? null,
        ]);

        return $this->response([
            'user'=>$user,
            'student'=>$student
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): Response {

        if ((auth()->user()->can(PermissionEnum::VIEW_ADVISEES->value)
            && !($student->faculty_id === auth()->user()->faculty_id))) {
            return $this->error(403, 'You do not have permission to view this student because you do not advise them.', 'forbidden');

        } else if (!(auth()->user()->can(PermissionEnum::VIEW_STUDENT_DETAILS->value))
            && !(auth()->user()->can(PermissionEnum::VIEW_ADVISEES->value))
            && !$this->isSelf($student)
        ) {
            return $this->error(403, 'You do not have permission to view this student.', 'forbidden');
        }

        $student->load('user', 'faculty', 'degreeProgram', 'degreeProgram.department', 'enrollments');

        return $this->response(StudentResource::make($student));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_STUDENTS->value)) {
            return $this->error(403, 'You do not have permission to update this student.', 'forbidden');
        }

        $data = $request->validated();

        $user = $student->user;

        $user->save();

        $student->save();

        return $this->response($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if(!auth()->user()->can(PermissionEnum::DELETE_STUDENTS->value)) {
            return $this->error(403, 'You do not have permission to delete this student.', 'forbidden');
        }

        $student->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Student deleted successfully.']);
    }

    public function organizationStudents(Request $request): Response|JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // check if user is faculty
        $faculty = $user->faculties()->first();
        if (!$faculty) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $department = $faculty->department;
        $organizationId = $department->organization_id;

        $query = Student::query()
            ->whereHas('degreeProgram.department', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });

        // Includes
        $allowedIncludes = ['user', 'faculty', 'degreeProgram', 'degreeProgram.department'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        (new StudentFilter())->apply($request, $query);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = StudentResource::collection($paginator->items());
        $meta = [
            'page' => $paginator->currentPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];

        return $this->response($data, $meta);
    }
}
