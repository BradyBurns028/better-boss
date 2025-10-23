<?php

namespace App\Models\Courses;

use App\Models\DegreeProgram;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class PlanOfStudy
 *
 * @property int $id
 * @property int $degree_program_id
 * @property int $student_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read DegreeProgram $degreeProgram
 * @property-read Student $student
 * @property-read Collection<int, Course> $courses
 * @property-read Collection<int, CourseSection> $sections
 *
 * @package App\Models\Courses
 */
class PlanOfStudy extends Model {

    protected $fillable = [
        'degree_program_id',
        'student_id',
    ];

    protected $casts = [
        'degree_program_id' => 'integer',
        'student_id' => 'integer',
    ];

    /**
     * Degree program this plan follows.
     *
     * @return BelongsTo<DegreeProgram, PlanOfStudy>
     */
    public function degreeProgram(): BelongsTo {
        return $this->belongsTo(DegreeProgram::class);
    }

    /**
     * Student who owns this plan.
     *
     * @return BelongsTo<Student, PlanOfStudy>
     */
    public function student(): BelongsTo {
        return $this->belongsTo(Student::class);
    }

    /**
     * Courses planned within this plan (via pivot).
     *
     * @return BelongsToMany<Course>
     */
    public function courses(): BelongsToMany {
        return $this->belongsToMany(
            Course::class,
            'planned_courses',
            'plan_of_study_id',
            'course_id'
        )->using(PlannedCoursePivot::class)
            ->withPivot(['year', 'term', 'status', 'course_section_id']);
    }

    /**
     * Specific sections selected in this plan (via pivot).
     *
     * Note: constrained to rows where course_section_id is not null.
     *
     * @return BelongsToMany<CourseSection>
     */
    public function sections(): BelongsToMany {
        return $this->belongsToMany(
            CourseSection::class,
            'planned_courses',
            'plan_of_study_id',
            'course_section_id'
        )->using(PlannedCoursePivot::class)
            ->withPivot(['course_id', 'year', 'term', 'status'])
            ->wherePivotNotNull('course_section_id');
    }
}
