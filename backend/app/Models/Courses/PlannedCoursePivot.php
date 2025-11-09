<?php

namespace App\Models\Courses;

use App\Enums\PlannedCourseEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlannedCoursePivot
 *
 * @property int $plan_of_study_id
 * @property int $course_id
 * @property int|null $course_section_id
 * @property int|null $year
 * @property string|null $term
 * @property PlannedCourseEnum $status
 *
 * @method static Builder|self status(PlannedCourseEnum $status)
 * @method static Builder|self planned()
 * @method static Builder|self active()
 * @method static Builder|self completed()
 * @method static Builder|self dropped()
 *
 * @package App\Models\Courses
 */
class PlannedCoursePivot extends Model {
    protected $table = 'planned_courses';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'plan_of_study_id',
        'course_id',
        'course_section_id',
        'year',
        'term',
        'status',
    ];

    protected $casts = [
        'year'   => 'integer',
        'status' => PlannedCourseEnum::class,
    ];

    /**
     * Scope by a specific status enum.
     *
     * @param Builder<self> $q
     * @param PlannedCourseEnum $status
     * @return Builder<self>
     */
    public function scopeStatus(Builder $q, PlannedCourseEnum $status): Builder {
        return $q->where('status', $status->value);
    }

    /**
     * Only planned rows.
     *
     * @param Builder<self> $q
     * @return Builder<self>
     */
    public function scopePlanned(Builder $q): Builder {
        return $q->where('status', PlannedCourseEnum::PLANNED->value);
    }

    /**
     * Only active rows.
     *
     * @param Builder<self> $q
     * @return Builder<self>
     */
    public function scopeActive(Builder $q): Builder {
        return $q->where('status', PlannedCourseEnum::ACTIVE->value);
    }

    /**
     * Only completed rows.
     *
     * @param Builder<self> $q
     * @return Builder<self>
     */
    public function scopeCompleted(Builder $q): Builder {
        return $q->where('status', PlannedCourseEnum::COMPLETED->value);
    }

    /**
     * Only dropped rows.
     *
     * @param Builder<self> $q
     * @return Builder<self>
     */
    public function scopeDropped(Builder $q): Builder {
        return $q->where('status', PlannedCourseEnum::DROPPED->value);
    }
}
