<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\DegreeProgram;
use Illuminate\Http\Request;
use App\Http\Filters\DegreeProgramFilter;
use App\Http\Resources\DegreeProgramResource;
use App\Http\Requests\StoreDegreeProgramRequest;
use App\Http\Requests\UpdateDegreeProgramRequest;
use App\Enums\PermissionEnum;

class DegreeProgramController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_DEGREE_PROGRAMS->value)){
            return $this->error(403, 'You do not have permission to view all degree programs.', 'forbidden');
        }

        $query = DegreeProgram::query();

        // Includes
        $allowedIncludes = ['department', 'programChair', 'students'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters via DegreeProgramFilter
        (new DegreeProgramFilter())->apply($request, $query);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = DegreeProgramResource::collection($paginator->items());
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
    public function store(StoreDegreeProgramRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_DEGREE_PROGRAMS->value)) {
            return $this->error(403, 'You do not have permission to create degree programs.', 'forbidden');
        }

        $data = $request->validated();

        $degreeProgram = DegreeProgram::create([
            'name' => $data['name'],
            'department_id' => $data['department_id'],
            'program_chair' => $data['program_chair'] ?? null,
        ]);

        return $this->response($degreeProgram);
    }

    /**
     * Display the specified resource.
     */
    public function show(DegreeProgram $degreeProgram)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_DEGREE_PROGRAMS->value)) {
            return $this->error(403, 'You do not have permission to view all degree programs.', 'forbidden');
        }

        $degreeProgram->load(['department', 'programChair', 'courses']);

        return $this->response(data: DegreeProgramResource::make($degreeProgram));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDegreeProgramRequest $request, DegreeProgram $degreeProgram)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_DEGREE_PROGRAMS->value)) {
            return $this->error(403, 'You do not have permission to edit degree programs.', 'forbidden');
        }

        $data = $request->validated();

        $degreeProgram->save();

        return $this->response($degreeProgram);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DegreeProgram $degreeProgram)
    {
        if(!auth()->user()->can(PermissionEnum::DELETE_DEGREE_PROGRAMS->value)) {
            return $this->error(403, 'You do not have permission to delete degree programs.', 'forbidden');
        }

        $degreeProgram->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Degree program deleted successfully.']);
    }
}
