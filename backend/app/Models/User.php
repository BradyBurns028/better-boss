<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\UserType;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @method static create(array $array)
 * 
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property UserType $user_type
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
class User extends Authenticatable
{
	use SoftDeletes, HasApiTokens, Notifiable, HasRoles, HasFactory;

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
        'user_type',
		'email_verified_at',
		'remember_token'
	];

    protected $casts = [
        'user_type' => UserType::class,
    ];

	public function admins(): HasOne
	{
		return $this->hasOne(Admin::class);
	}

	public function organizations(): HasOne
	{
		return $this->hasOne(Organization::class, 'owner_id');
	}

	public function students(): HasOne
	{
		return $this->hasOne(Student::class);
	}

	public function faculties(): HasOne
	{
		return $this->hasOne(Faculty::class);
	}

    /**
     * Returns the organization model for a faculty or student, null for admin
     *
     * @return Attribute
     */
    protected function organization(): Attribute {
        return Attribute::get(function () {
            if ($this->hasRole('admin') || $this->user_type === UserType::ADMIN) {
                return null;
            }

            $student = $this->students()
                ->with('degreeProgram.department.organization')
                ->first();

            if ($student?->degreeProgram?->department?->organization) {
                return $student->degreeProgram->department->organization;
            }

            $faculty = $this->faculties()
                ->with('department.organization')
                ->first();

            return $faculty?->department?->organization;
        });
    }

    protected static function booted(){
        static::created(function (User $user) {
            if ($user->user_type && !$user->roles()->exists()) {
                // Assign role matching the enum value (e.g. 'faculty', 'admin')
                $user->assignRole($user->user_type->value);
            }
        });
    }
}
