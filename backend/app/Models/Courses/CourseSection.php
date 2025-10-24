<?php

namespace App\Models\Courses;

use App\Models\Faculty;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class CourseSection
 *
 * @property int $id
 * @property int $course_id
 * @property int $section_number
 * @property string $term Term label (e.g., "Fall", "Spring")
 * @property int $year Calendar/academic year (e.g., 2025)
 * @property string|null $time Meeting time (cast as 'time', e.g., "13:30:00")
 * @property int|null $instructor_id
 * @property int|null $capacity
 * @property string|null $room_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Course $course
 * @property-read Faculty|null $instructor
 * @property-read Collection<int, PlanOfStudy> $plans
 *
 * @package App\Models\Courses
 */
class CourseSection extends Model {

    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_number',
        'term',
        'year',
        'time',
        'instructor_id',
        'capacity',
        'room_number',
    ];

    /**
     * Parent course for this section.
     *
     * @return BelongsTo<Course, CourseSection>
     */
    public function course(): BelongsTo {
        return $this->belongsTo(Course::class);
    }

    /**
     * Instructor teaching this section (if assigned).
     *
     * @return BelongsTo<Faculty, CourseSection>
     */
    public function instructor(): BelongsTo {
        return $this->belongsTo(Faculty::class, 'instructor_id');
    }

    /**
     * Plans of study that target this specific section (via pivot).
     *
     * Note: constrained to rows where course_section_id is not null.
     *
     * @return BelongsToMany<PlanOfStudy>
     */
    public function plans(): BelongsToMany {
        return $this->belongsToMany(
            PlanOfStudy::class,
            'planned_courses',
            'course_section_id',
            'plan_of_study_id'
        )->withPivot(['course_id', 'year', 'term', 'status'])
            ->wherePivotNotNull('course_section_id');
    }
}
