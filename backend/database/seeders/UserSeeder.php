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
            ->state(['user_type' => UserType::ADMIN->value])
            ->create();

        $adminUsers->each(fn (User $u) => Admin::firstOrCreate(['user_id' => $u->id]));

        /*$facultyUsers = User::factory()
            ->count($requiredFaculty)
            ->state(['user_type' => UserType::FACULTY->value])
            ->create();

        $facultyUsers->each(fn (User $u) =>
            Faculty::firstOrCreate([
                'user_id' => $u->id
            ])
        );

        User::factory()
            ->count(10)
            ->state(['user_type' => UserType::STUDENT->value])
            ->create();*/
    }
}
