<?php

namespace App\Http\Controllers;

use App\Models\Courses\PlannedCoursePivot;
use Illuminate\Http\Request;
use App\Http\Resources\PlannedCoursePivotResource;
use App\Http\Requests\StorePlannedCoursePivotRequest;
use App\Http\Requests\UpdatePlannedCoursePivotRequest;
use App\Http\Filters\PlannedCoursePivotFilter;
use Symfony\Component\HttpFoundation\Response;

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
     * Updates, creates, and deletes the planned course pivot
     */
    public function store(StorePlannedCoursePivotRequest $request): Response
    {
        $data = $request->validated();

        $keys = [
            'plan_of_study_id' => $data['plan_of_study_id'],
            'course_id' => $data['course_id'],
        ];

        $attrs = [
            'course_section_id' => $data['course_section_id'] ?? null,
            'year' => $data['year'] ?? null,
            'term' => $data['term'] ?? null,
            'status' => $data['status'] ?? 'planned',
        ];

        $exists = PlannedCoursePivot::query()
            ->where($keys)
            ->exists();

        if ($exists) {
            $term = $data['term'] ?? null;
            $year = $data['year'] ?? null;
            if (is_null($term) && is_null($year)) {
                $deleted = PlannedCoursePivot::query()->where($keys)->delete();
                if ($deleted === 0) {
                    return $this->response(['status' => 200, 'message' => 'Nothing to delete.']);
                }
                return $this->response(['status' => 200, 'message' => 'Planned course removed.']);
            }

            PlannedCoursePivot::query()
                ->where($keys)
                ->update($attrs);
        } else {
            PlannedCoursePivot::create($keys + $attrs);
        }

        $pivot = PlannedCoursePivot::query()->where($keys)->first();

        return $this->response(PlannedCoursePivotResource::make($pivot));
    }

    /**
     * Display the specified resource.
     */
    public function show(PlannedCoursePivot $plannedCoursePivot)
    {
        return $this->response(data: PlannedCoursePivotResource::make($plannedCoursePivot));
    }
}
