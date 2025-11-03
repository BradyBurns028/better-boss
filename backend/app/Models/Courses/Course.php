<?php

namespace App\Models\Courses;

use App\Models\DegreeProgram;
use App\Models\Department;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Course
 *
 * @property int $id
 * @property string $course_code
 * @property string $name
 * @property string|null $description
 * @property int $credits
 * @property int $department_id
 * @property int|null $prerequisite_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Department $department
 * @property-read Course|null $prerequisite
 * @property-read Collection<int, Course> $dependents
 * @property-read Collection<int, CourseSection> $sections
 * @property-read Collection<int, DegreeProgram> $degreeRequirements
 * @property-read Collection<int, PlanOfStudy> $plans
 *
 * @method static Builder|Course code(string $code)
 *
 * @package App\Models\Courses
 */
class Course extends Model {

    use HasFactory;

    protected $fillable = [
        'course_code',
        'name',
        'description',
        'credits',
        'department_id',
        'prerequisite_id',
    ];

    /**
     * Eloquent relationship with the department that owns the course.
     *
     * @return BelongsTo<Department, Course>
     */
    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }

    /**
     * Eloquent relationship with a course required to take this course.
     *
     * @return BelongsTo<Course, Course>
     */
    public function prerequisite(): BelongsTo {
        return $this->belongsTo(Course::class, 'prerequisite_id');
    }

    /**
     * Eloquent relationship with all the courses that require this course.
     *
     * @return HasMany<Course>
     */
    public function dependents(): HasMany {
        return $this->hasMany(Course::class, 'prerequisite_id');
    }

    /**
     * Eloquent relationship for all the sections of the course.
     *
     * @return HasMany<CourseSection>
     */
    public function sections(): HasMany {
        return $this->hasMany(CourseSection::class);
    }

    /**
     * Eloquent relationship for the program degree requirements
     * to which this course belongs.
     *
     * @return BelongsToMany<DegreeProgram>
     */
    public function degreeRequirements(): BelongsToMany {
        return $this->belongsToMany(DegreeProgram::class, 'degree_requirements')
            ->withPivot(['course_set', 'minimum_grade']);
    }

    /**
     * Eloquent relationship for all the plans this courses
     * is a part of.
     *
     * @return BelongsToMany<PlanOfStudy>
     */
    public function plans(): BelongsToMany {
        return $this->belongsToMany(
            PlanOfStudy::class,
            'planned_courses',
            'course_id',
            'plan_of_study_id'
        )->using(PlannedCoursePivot::class)
            ->withPivot(['year', 'term', 'status', 'course_section_id']);
    }

    /**
     * Scope to call the course using its course code.
     *
     * Ex. Course::code('CS101')->first();
     *
     * @param Builder<Course> $query
     * @param string $code
     * @return Builder<Course>
     */
    public function scopeCode(Builder $query, string $code): Builder {
        return $query->where('course_code', $code);
    }

    /**
     * Allows the course to easily be filtered to an organization
     *
     * Ex: Course::forOrganization(1)
     *
     * @param Builder $query
     * @param int $orgId
     * @return Builder
     */
    public function scopeForOrganization(Builder $query, int $orgId): Builder {
        return $query->whereHas('department', fn ($q) => $q->where('organization_id', $orgId));
    }
}
