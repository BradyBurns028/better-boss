<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
class Student extends Model
{

	protected $fillable = [
		'degree_program',
		'user_id',
		'faculty_id'
	];

	public function degree_program(): BelongsTo
	{
		return $this->belongsTo(DegreeProgram::class, 'degree_program');
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function faculty(): BelongsTo
	{
		return $this->belongsTo(Faculty::class);
	}
}
