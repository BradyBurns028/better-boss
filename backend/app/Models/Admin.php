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

/**
 * Class Admin
 *
 * @method static firstOrCreate(array $array)
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Collection|Organization[] $organizations
 *
 * @package App\Models
 */
class Admin extends Model {
    use HasFactory;

	protected $fillable = [
		'user_id'
	];

	public function user(): BelongsTo {
		return $this->belongsTo(User::class);
	}

	public function organizations(): HasMany {
		return $this->hasMany(Organization::class);
	}
}
