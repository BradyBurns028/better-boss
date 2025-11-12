<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Courses\Course;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class DegreeProgram
 * 
 * @property int $id
 * @property string $name
 * @property int $department_id
 * @property int $program_chair
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Department $department
 * @property Faculty $faculty
 * @property Collection|Student[] $students
 * @method static Builder|Course code(string $code)
 * @method static Builder|Course forOrganization(int $orgId)
 * @method static create(array $array)
 * @package App\Models
 */
class DegreeProgram extends Model {

    use HasFactory;

	protected $fillable = [
		'name',
		'department_id',
		'program_chair'
	];

	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class);
	}

	public function programChair(): BelongsTo
	{
		return $this->belongsTo(Faculty::class, 'program_chair');
	}

	public function students(): HasMany
	{
		return $this->hasMany(Student::class, 'degree_program');
	}

    /**
     * Courses that satisfy this degree program's requirements.
     *
     * @return BelongsToMany<Course>
     */
    public function courses(): BelongsToMany {
        return $this->belongsToMany(
            Course::class,
            'degree_requirements',
            'degree_program_id',
            'course_id'
        )->withPivot(['course_set', 'minimum_grade']);
    }

    public function scopeForOrganization(Builder $query, int $orgId): Builder {
        return $query->whereHas('department', fn ($q) => $q->where('organization_id', $orgId));
    }
}
