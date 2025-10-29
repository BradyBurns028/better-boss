<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;

class DepartmentController extends AbstractController
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
    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();

        $department = Department::create([
            'name' => $data['name'],
            'organization_id' => $data['organization_id'],
            'department_chair' => $data['department_chair'] ?? null,
        ]);

        return $this->response($department);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load(['organization', 'degreePrograms', 'faculty', 'departmentChair']);

        return $this->response(data: DepartmentResource::make($department));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $data = $request->validated();

        $department->save();

        return $this->response($department);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
    }
}
