<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Courses\PlanOfStudy;
use Illuminate\Http\Request;
use App\Http\Resources\PlanOfStudyResource;
use App\Http\Requests\StorePlanOfStudyRequest;
use App\Http\Requests\UpdatePlanOfStudyRequest;
use App\Http\Filters\PlanOfStudyFilter;
use App\Enums\PermissionEnum;

class PlanOfStudyController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlanOfStudy::query();

        $user = auth()->user();

        if($user->user_type === 'student') {
            $query->where('student_id', $user->student->id);
        } else if($user->user_type === 'faculty' && $user->faculty->role_type->equals('instructor')) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('advisor_id', $user->faculty->id);
            });
        }

        if(!$user->can('index_plans_of_study')) {
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
        if(!auth()->user()->can(PermissionEnum::VIEW_PLANS_OF_STUDY->value)
            || !(auth()->user() == $planOfStudy->student->user)) {
            return $this->error(403, 'You do not have permission to view this plan of study.', 'forbidden');

        } else if (auth()->user()->user_type === 'faculty'
            && auth()->user()->faculty->role_type->equals('instructor')) {
            if($planOfStudy->student->advisor_id !== auth()->user()->faculty->id) {
                return $this->error(403, 'You do not have permission to view this plan of study.', 'forbidden');
            }
        }

        $planOfStudy->load(['degreeProgram', 'student', 'courses', 'sections']);

        return $this->response(data: PlanOfStudyResource::make($planOfStudy));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanOfStudyRequest $request, PlanOfStudy $planOfStudy)
    {
        $user = auth()->user();

        if(!($user == $planOfStudy->student->user)
            || !$user->can(PermissionEnum::EDIT_PLANS_OF_STUDY->value)) {

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
        $user = auth()->user();

        if(!$user->can(PermissionEnum::DELETE_PLANS_OF_STUDY->value)
            || !($user == $planOfStudy->student->user)) {

            return $this->error(403, 'You do not have permission to delete this plan of study.', 'forbidden');
        }

        $planOfStudy->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Plan of study deleted successfully.']);
    }
}
