<?php

namespace Database\Seeders;

use App\Enums\UserType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder {
    public function run(): void {
        foreach (UserType::cases() as $type) {
            Role::query()->firstOrCreate(['name' => $type->value]);
        }
    }
}
