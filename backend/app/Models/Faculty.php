<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\FacultyRoleTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Faculty
 *
 * @method static firstOrCreate(array $array)
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property string|null $office
 * @property string $role_type
 * @property int $department_id
 * 
 * @property User $user
 * @property Department $department
 * @property Collection|DegreeProgram[] $degree_programs
 * @property Collection|Student[] $students
 * @property Collection|Department[] $departments
 *
 * @package App\Models
 */
class Faculty extends Model {

    use HasFactory;

	protected $fillable = [
		'user_id',
		'office',
		'role_type',
		'department_id'
	];

    protected $casts = [
        'role_type' => FacultyRoleTypeEnum::class,
    ];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class);
	}

	public function degreePrograms(): HasMany
	{
		return $this->hasMany(DegreeProgram::class, 'program_chair');
	}

	public function advisees(): HasMany
	{
		return $this->hasMany(Student::class);
	}

	public function departments(): HasOne
	{
		return $this->hasOne(Department::class, 'department_chair');
	}

    protected function organization(): Attribute {
        return Attribute::get(fn () => $this->department?->organization);
    }
}
