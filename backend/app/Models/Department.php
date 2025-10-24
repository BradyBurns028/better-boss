<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Department
 * 
 * @property int $id
 * @property string $name
 * @property int $organization_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $department_chair
 * 
 * @property Organization $organization
 * @property Faculty $faculty
 * @property Collection|DegreeProgram[] $degree_programs
 * @property Collection|Faculty[] $faculties
 *
 * @package App\Models
 */
class Department extends Model
{
	use SoftDeletes, HasFactory;

	protected $fillable = [
		'name',
		'organization_id',
		'department_chair'
	];

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class);
	}

	public function departmentChair(): BelongsTo
	{
		return $this->belongsTo(Faculty::class, 'department_chair');
	}

	public function degreePrograms(): HasMany
	{
		return $this->hasMany(DegreeProgram::class);
	}

	public function faculty(): HasMany
	{
		return $this->hasMany(Faculty::class);
	}
}
