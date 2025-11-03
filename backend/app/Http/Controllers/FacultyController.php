<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Http\Filters\FacultyFilter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\FacultyResource;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use App\Enums\PermissionEnum;

class FacultyController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faculty::query();

        if(auth()->user()->can(PermissionEnum::VIEW_INSTRUCTORS->value)
            && !auth()->user()->can(PermissionEnum::VIEW_ADMINISTRATORS->value)
            && !auth()->user()->can(PermissionEnum::VIEW_STAFF->value)
        ) {
            $query->where('role_type', 'instructor');

        } else if(auth()->user()->can(PermissionEnum::VIEW_STAFF->value)
            && !auth()->user()->can(PermissionEnum::VIEW_ADMINISTRATORS->value)
            && !auth()->user()->can(PermissionEnum::VIEW_INSTRUCTORS->value)
        ) {
            $query->where('role_type', 'staff');

        } else if(
            auth()->user()->can(PermissionEnum::VIEW_ADMINISTRATORS->value)
            && !auth()->user()->can(PermissionEnum::VIEW_INSTRUCTORS->value)
            && !auth()->user()->can(PermissionEnum::VIEW_STAFF->value)
        ) {
            $query->where('role_type', 'administrator');

        } else if(!auth()->user()->can(PermissionEnum::VIEW_FACULTY->value)) {
            return $this->error(403, 'You do not have permission to view faculty.', 'forbidden');
        }

        // Includes
        $allowedIncludes = ['user', 'department', 'degreePrograms', 'advisees'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters
        (new FacultyFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'department_id', 'role_type', 'created_at'];
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

        $data = FacultyResource::collection($paginator->items());
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
    public function store(StoreFacultyRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_FACULTY->value)) {
            return $this->error(403, 'You do not have permission to create faculty.', 'forbidden');
        }

        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? 'faculty',
        ]);

        $faculty = Faculty::create([
            'user_id' => $user->id,
            'office' => $data['office'] ?? null,
            'role_type' => $data['role_type'],
            'department_id' => $data['department_id'],
        ]);

        return $this->response([
            'user' => $user,
            'faculty' => $faculty,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        if(
            !(auth()->user()->can(PermissionEnum::VIEW_FACULTY->value))
            || !(auth()->user()->can(PermissionEnum::VIEW_ADMINISTRATORS->value)
                && $faculty->role_type === 'administrator')
            || !(auth()->user()->can(PermissionEnum::VIEW_INSTRUCTORS->value)
                && $faculty->role_type === 'instructor')
            || !(auth()->user()->can(PermissionEnum::VIEW_STAFF->value)
                && $faculty->role_type === 'staff')
        ) {
            return $this->error(403, 'You do not have permission to view this faculty.', 'forbidden');
        }

        $faculty->load('user', 'department', 'degreePrograms');
            if($faculty->role_type === 'instructor') {
                $faculty->load('advisees');
            }

            return $this->response(data: FacultyResource::make($faculty));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_FACULTY->value)) {
            return $this->error(403, 'You do not have permission to update faculty.', 'forbidden');
        }

        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $faculty->user_id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'office' => 'sometimes|nullable|string|max:255',
            'role_type' => 'sometimes|required|string',
            'department_id' => 'sometimes|required|integer|exists:departments,id',
        ]);

        $user = $faculty->user;

        $user->save();

        $faculty->save();

        return $this->response($faculty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        if(!auth()->user()->can(PermissionEnum::DELETE_FACULTY->value)) {
            return $this->error(403, 'You do not have permission to delete faculty.', 'forbidden');
        }

        $faculty->user->delete();
        $faculty->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Faculty deleted successfully.']);
    }
}
