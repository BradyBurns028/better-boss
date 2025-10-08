<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * 
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * 
 * @property Collection|Admin[] $admins
 * @property Collection|Organization[] $organizations
 * @property Collection|Student[] $students
 * @property Collection|Faculty[] $faculties
 *
 * @package App\Models
 */
class User extends Model
{
	use SoftDeletes;

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'email_verified_at',
		'remember_token'
	];

	public function admins(): HasMany
	{
		return $this->hasMany(Admin::class);
	}

	public function organizations(): HasMany
	{
		return $this->hasMany(Organization::class, 'owner_id');
	}

	public function students(): HasMany
	{
		return $this->hasMany(Student::class);
	}

	public function faculties(): HasMany
	{
		return $this->hasMany(Faculty::class);
	}
}
