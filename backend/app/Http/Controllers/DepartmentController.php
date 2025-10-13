<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Resources\DepartmentResource;

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
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|integer|exists:organizations,id',
            'department_chair' => 'sometimes|nullable|integer|exists:faculties,id',
        ]);

        $department = Department::create([
            'name' => $data['name'],
            'organization_id' => $data['organization_id'],
            'department_chair' => $data['department_chair'] ?? null,
        ]);

        return $department;
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
    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'organization_id' => 'sometimes|required|integer|exists:organizations,id',
            'department_chair' => 'sometimes|nullable|integer|exists:faculties,id',
        ]);

        if (isset($data['name'])) $department->name = $data['name'];
        if (isset($data['organization_id'])) $department->organization_id = $data['organization_id'];
        if (array_key_exists('department_chair', $data)) $department->department_chair = $data['department_chair'];

        $department->save();

        return $department;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
    }
}
