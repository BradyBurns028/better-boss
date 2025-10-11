<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 *
 * @package App\Models
 */
class DegreeProgram extends Model
{

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
}
