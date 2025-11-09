<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\CheckSelfAccess;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Enums\UserType;
use App\Enums\PermissionEnum;
use App\Models\Student;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use App\Http\Filters\UserFilter;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    use CheckSelfAccess;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_USERS->value)) {
            return $this->error(403, 'You do not have permission to view users.', 'forbidden');
        }

        $query = User::query();

        $query->with([
            'admins',
            'students.degreeProgram.department.organization',
            'faculties.department.organization',
            'students.faculty.user',
        ]);

        // Filters
        (new UserFilter())->apply($request, $query);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = UserResource::collection($paginator->items());
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
    public function store(StoreUserRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_USERS->value)) {
            return $this->error(403, 'You do not have permission to create users.', 'forbidden');
        }

        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? null,
        ]);

        $user->load(['admins', 'organizations', 'students', 'faculties']);

        return $this->response(data: UserResource::make($user));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_USERS->value)
        && !$this->isself($user)
        ) {
            return $this->error(403, 'You do not have permission to view users.', 'forbidden');
        }

        $user->load(['admins', 'organizations', 'students', 'faculties']);

        return $this->response(data: UserResource::make($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        if(!auth()->user()->can(PermissionEnum::EDIT_USERS->value)) {
            // Allow only password changes for the authenticated user
            if (auth()->user()->id !== $user->id) {
                return $this->error(403, 'You do not have permission to edit this user.', 'forbidden');
            }

            $data = $request->only(['password']);

            if (empty($data['password'])) {
                return $this->error(400, 'Password is required.', 'bad_request');
            }

            $user->password = bcrypt($data['password']);
            $user->save();

            return $this->response(data: UserResource::make($user));
        }

        $data = $request->validated();

        // Handle password hashing if present
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Mass assign allowed fields
        $user->fill($data);
        $user->save();

        $user = $user->fresh(['admins', 'organizations', 'students', 'faculties']);

        return $this->response(data: UserResource::make($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if(!auth()->user()->can(PermissionEnum::DELETE_USERS->value)) {
            return $this->error(403, 'You do not have permission to delete users.', 'forbidden');
        }

        $user->delete();

        return $this->response(data: ['status' => 200, 'message' => 'User deleted successfully.']);
    }
}
