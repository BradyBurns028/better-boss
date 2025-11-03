<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Courses\CourseSection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Student
 * 
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $degree_program
 * @property int $user_id
 * @property int $faculty_id
 * 
 * @property User $user
 * @property Faculty $faculty
 *
 * @package App\Models
 */
class Student extends Model {
    use HasFactory;

	protected $fillable = [
		'degree_program',
		'user_id',
		'faculty_id'
	];

	public function degreeProgram(): BelongsTo {
		return $this->belongsTo(DegreeProgram::class, 'degree_program');
	}

	public function user(): BelongsTo {
		return $this->belongsTo(User::class);
	}

	public function faculty(): BelongsTo {
		return $this->belongsTo(Faculty::class);
	}

    /**
     * Sections this student is enrolled in (via enrollments pivot).
     *
     * @return BelongsToMany<CourseSection>
     */
    public function enrollments(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseSection::class,
            'enrollments',
            'student_id',
            'course_section_id'
        )->withTimestamps();
    }

    public function organization(): Attribute {
        return Attribute::get(fn () =>
            $this->degreeProgram?->department?->organization
        );
    }
}
