<?php

namespace App\Http\Controllers;

use App\Models\Courses\PlannedCoursePivot;
use Illuminate\Http\Request;
use App\Http\Resources\PlannedCoursePivotResource;
use App\Http\Requests\StorePlannedCoursePivotRequest;
use App\Http\Requests\UpdatePlannedCoursePivotRequest;
use App\Http\Filters\PlannedCoursePivotFilter;

class PlannedCoursePivotController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlannedCoursePivot::query();

        // Filters
        (new PlannedCoursePivotFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['plan_of_study_id', 'course_id', 'course_section_id', 'year', 'term', 'status'];
        $sort = (string) $request->query('sort', 'year');
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'year';
        }
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = PlannedCoursePivotResource::collection($paginator->items());
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
    public function store(StorePlannedCoursePivotRequest $request)
    {
        $data = $request->validated();

        $plannedCoursePivot = PlannedCoursePivot::create([
            'plan_of_study_id' => $data['plan_of_study_id'],
            'course_id' => $data['course_id'],
            'course_section_id' => $data['course_section_id'] ?? null,
            'year' => $data['year'] ?? null,
            'term' => $data['term'] ?? null,
            'status' => $data['status'],
        ]);

        return $this->response($plannedCoursePivot);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlannedCoursePivot $plannedCoursePivot)
    {
        return $this->response(data: PlannedCoursePivotResource::make($plannedCoursePivot));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlannedCoursePivotRequest $request, PlannedCoursePivot $plannedCoursePivot)
    {
        $data = $request->validated();

        $plannedCoursePivot->save();

        return $this->response($plannedCoursePivot);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlannedCoursePivot $plannedCoursePivot)
    {
        //
    }
}
