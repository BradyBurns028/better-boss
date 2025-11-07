<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Filters\DepartmentFilter;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Enums\PermissionEnum;

class DepartmentController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_DEPARTMENTS->value)){
            return $this->error(403, 'You do not have permission to view all departments.', 'forbidden');
        }

        $query = Department::query();

        // Includes
        $allowedIncludes = ['organization', 'degreePrograms', 'departmentChair', 'faculty'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters via DepartmentFilter
        (new DepartmentFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'name', 'organization_id', 'created_at'];
        $sort = (string) $request->query('sort', 'name');
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = DepartmentResource::collection($paginator->items());
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
    public function store(StoreDepartmentRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_DEPARTMENTS->value)) {
            return $this->error(403, 'You do not have permission to create departments.', 'forbidden');
        }

        $data = $request->validated();

        try {
            $department = Department::create([
                'name' => $data['name'],
                'organization_id' => $data['organization_id'],
                'department_chair' => $data['department_chair'] ?? null,
            ]);
            return $this->response($department);
        } catch (\Exception $exception) {
            return $this->error(500, $exception->getMessage(), 'internal_server_error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_DEPARTMENTS->value)) {
            return $this->error(403, 'You do not have permission to view departments.', 'forbidden');
        }

        $department->load(['organization', 'degreePrograms', 'faculty', 'departmentChair']);

        return $this->response(data: DepartmentResource::make($department));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_DEPARTMENTS->value)) {
            return $this->error(403, 'You do not have permission to update departments.', 'forbidden');
        }

        $data = $request->validated();

        $department->save();

        return $this->response($department);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if(!auth()->user()->can(PermissionEnum::DELETE_DEPARTMENTS->value)) {
            return $this->error(403, 'You do not have permission to delete departments.', 'forbidden');
        }

        $department->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Department deleted successfully.']);
    }
}
