<?php

namespace App\Http\Controllers;

use App\Models\Courses\PlanOfStudy;
use Illuminate\Http\Request;
use App\Http\Resources\PlanOfStudyResource;
use App\Http\Requests\StorePlanOfStudyRequest;
use App\Http\Requests\UpdatePlanOfStudyRequest;
use App\Http\Filters\PlanOfStudyFilter;
use app\Enums\PermissionEnum;

class PlanOfStudyController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlanOfStudy::query();

        if(!auth()->user()->can(PermissionEnum::VIEW_PLANS_OF_STUDY->value)) {
            return $this->error(403, 'You do not have permission to view plans of study.', 'forbidden');
        }

        // Includes (supports nested include degreeProgram.department)
        $allowedIncludes = ['degreeProgram', 'degreeProgram.department', 'student', 'courses', 'sections'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters
        (new PlanOfStudyFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'degree_program_id', 'student_id', 'created_at'];
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

        $data = PlanOfStudyResource::collection($paginator->items());
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
    public function store(StorePlanOfStudyRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_PLANS_OF_STUDY->value)) {
            return $this->error(403, 'You do not have permission to create a plan of study.', 'forbidden');
        }

        $data = $request->validated();

        $planOfStudy = PlanOfStudy::create([
            'degree_program_id' => $data['degree_program_id'],
            'student_id' => $data['student_id'],
        ]);

        return $this->response($planOfStudy);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanOfStudy $planOfStudy)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_PLANS_OF_STUDY->value)) {
            return $this->error(403, 'You do not have permission to view this plan of study.', 'forbidden');
        }

        $planOfStudy->load(['degreeProgram', 'student', 'courses', 'sections']);

        return $this->response(data: PlanOfStudyResource::make($planOfStudy));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanOfStudyRequest $request, PlanOfStudy $planOfStudy)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_PLANS_OF_STUDY->value)) {
            return $this->error(403, 'You do not have permission to update this plan of study.', 'forbidden');
        }

        $data = $request->validated();

        $planOfStudy->save();

        return $this->response($planOfStudy);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanOfStudy $planOfStudy)
    {
        //
    }
}
