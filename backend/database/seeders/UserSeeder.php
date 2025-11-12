<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Admin;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class UserSeeder extends Seeder {
    public function run(): void {
        /** @var Collection<int,User> $adminUsers */
        $adminUsers = User::factory()
            ->count(7)
            ->admin()
            ->create();

        $adminUsers->each(fn (User $u) => Admin::firstOrCreate(['user_id' => $u->id]));
    }
}
