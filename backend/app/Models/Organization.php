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
 * Class Organization
 * 
 * @property int $id
 * @property string $name
 * @property int $admin_id
 * @property int|null $owner_id
 * @property string $address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Admin $admin
 * @property User|null $user
 * @property Collection|Department[] $departments
 *
 * @package App\Models
 */
class Organization extends Model {
	use SoftDeletes, HasFactory;

	protected $fillable = [
		'name',
		'admin_id',
		'owner_id',
		'address'
	];

	public function admin(): BelongsTo {
		return $this->belongsTo(Admin::class);
	}

	public function user(): BelongsTo {
		return $this->belongsTo(User::class, 'owner_id');
	}

	public function departments(): HasMany {
		return $this->hasMany(Department::class);
	}
}
