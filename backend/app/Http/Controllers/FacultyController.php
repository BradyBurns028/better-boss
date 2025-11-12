<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Courses\PlanOfStudy;
use App\Models\Courses\PlannedCoursePivot;
use App\Models\Courses\CourseSection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Filters\FacultyFilter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\FacultyResource;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use App\Enums\PermissionEnum;
use App\Traits\CheckSelfAccess;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FacultyController extends AbstractController
{
    use CheckSelfAccess;

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
            && !(auth()->user()->can(PermissionEnum::VIEW_ADMINISTRATORS->value)
                && $faculty->role_type === 'administrator')
            && !(auth()->user()->can(PermissionEnum::VIEW_INSTRUCTORS->value)
                && $faculty->role_type === 'instructor')
            && !(auth()->user()->can(PermissionEnum::VIEW_STAFF->value)
                && $faculty->role_type === 'staff')
            && !$this->isSelf($faculty)
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

    /**
     * Enroll all advisees (students assigned to the authenticated faculty) into
     * the course sections in their plan of study for the current term/year.
     */
    public function enrollCurrentTerm(Request $request): Response
    {
        $user = auth()->user();

        // must be a faculty and have permission to view advisees
        if (!$user || !$user->hasRole('faculty') || !$user->can(PermissionEnum::VIEW_ADVISEES->value)) {
            return $this->error(403, 'Only faculty with advisee access can perform enrollments.', 'forbidden');
        }

        /** @var Faculty|null $faculty */
        $faculty = $user->faculties()->first();
        if (!$faculty) {
            return $this->error(403, 'Faculty profile not found for the current user.', 'forbidden');
        }

        // provide option to include term/year
        $term = $request->input('term');
        $year = $request->input('year');
        if (!$term || !$year) {
            [$termGuess, $yearGuess] = $this->getCurrentTermAndYear();
            $term ??= $termGuess;
            $year ??= $yearGuess;
        }

        // load advisees
        /** @var \Illuminate\Database\Eloquent\Collection<int, Student> $advisees */
        $advisees = $faculty->advisees()->get();

        $summary = [
            'term' => $term,
            'year' => (int) $year,
            'students_processed' => 0,
            'enrollments_attempted' => 0,
            'enrollments_created' => 0,
            'per_student' => [],
        ];

        foreach ($advisees as $student) {
            $studentResult = [
                'student_id' => $student->id,
                'planned_sections' => 0,
                'enrolled' => 0,
                'skipped' => 0,
            ];

            // find all plan-of-study rows for this student
            $plans = PlanOfStudy::query()
                ->where('student_id', $student->id)
                ->pluck('id');

            if ($plans->isEmpty()) {
                $summary['per_student'][] = $studentResult;
                $summary['students_processed']++;
                continue;
            }

            // planned rows for this term/year with a concrete section selected
            $planned = PlannedCoursePivot::query()
                ->whereIn('plan_of_study_id', $plans)
                ->where('term', $term)
                ->where('year', (int) $year)
                ->whereNotNull('course_section_id')
                ->planned()
                ->get(['plan_of_study_id', 'course_section_id']);

            $studentResult['planned_sections'] = $planned->count();
            $summary['enrollments_attempted'] += $planned->count();

            foreach ($planned as $row) {
                // attach enrollment without detaching existing
                $attached = $student->enrollments()
                    ->syncWithoutDetaching([$row->course_section_id => ['grade' => null]]);

                // syncWithoutDetaching returns arrays of attached ids under 'attached'
                $justAttachedCount = isset($attached['attached']) ? count($attached['attached']) : 0;
                if ($justAttachedCount > 0) {
                    $summary['enrollments_created'] += $justAttachedCount;
                    $studentResult['enrolled'] += $justAttachedCount;

                    // Flip planned row to active now that the student is enrolled
                    PlannedCoursePivot::query()
                        ->where('plan_of_study_id', $row->plan_of_study_id)
                        ->where('course_section_id', $row->course_section_id)
                        ->where('term', $term)
                        ->where('year', (int) $year)
                        ->update(['status' => 'active']);
                } else {
                    $studentResult['skipped']++;
                }
            }

            $summary['per_student'][] = $studentResult;
            $summary['students_processed']++;
        }

        return $this->response($summary);
    }

    /**
     * Get institution term label and year from current date.
     * Spring: Jan–Apr, Summer: May–Aug, Fall: Sep–Dec.
     * @return array{0:string,1:int}
     */
    private function getCurrentTermAndYear(): array
    {
        $now = Carbon::now();
        $month = (int) $now->month;
        $year = (int) $now->year;
        if ($month >= 1 && $month <= 4) {
            return ['Spring', $year];
        }
        if ($month >= 5 && $month <= 8) {
            return ['Summer', $year];
        }
        return ['Fall', $year];
    }
}
