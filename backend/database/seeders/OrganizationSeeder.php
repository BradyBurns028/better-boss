<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class OrganizationSeeder extends Seeder {
    public function run(): void {
        /** @var Collection<int,User> $adminUsers */
        $adminUsers = User::factory()
            ->count(3)
            ->state(['user_type' => UserType::ADMIN->value])
            ->create();

        $adminUsers->each(fn (User $u) => Admin::firstOrCreate(['user_id' => $u->id]));

        $organizations = User::factory()
            ->count(3)
            ->state([
                'user_type' => UserType::ADMIN->value
            ])
            ->create();

    }
}
