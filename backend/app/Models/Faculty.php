<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faculty
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
class Faculty extends Model
{

	protected $fillable = [
		'user_id',
		'office',
		'role_type',
		'department_id'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class);
	}

	public function degree_programs(): HasOne
	{
		return $this->hasOne(DegreeProgram::class, 'program_chair');
	}

	public function students(): HasMany
	{
		return $this->hasMany(Student::class);
	}

	public function departments(): HasOne
	{
		return $this->hasOne(Department::class, 'department_chair');
	}
}
