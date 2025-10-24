<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class UserSeeder extends Seeder {
    public function run(): void {
        $orgCount = 3;
        $deptsPerOrg = 5;
        $instrPerDept = 3;
        $extraAdmins = 2;
        $extraOwners = 3;
        $extraFaculty = 3;

        $requiredAdmins  = $orgCount + $extraAdmins;
        $requiredOwners  = $orgCount + $extraOwners;
        $requiredChairs  = $orgCount * $deptsPerOrg;
        $requiredInstr   = $orgCount * $deptsPerOrg * $instrPerDept;
        $requiredFaculty = $requiredOwners + $requiredChairs + $requiredInstr + $extraFaculty;

        /** @var Collection<int,User> $adminUsers */
        $adminUsers = User::factory()
            ->count($requiredAdmins)
            ->state(['user_type' => UserType::ADMIN->value])
            ->create();

        $adminUsers->each(fn (User $u) => Admin::firstOrCreate(['user_id' => $u->id]));

        User::factory()
            ->count($requiredFaculty)
            ->state(['user_type' => UserType::FACULTY->value])
            ->create();

        // Students (optional – create a buffer for future seeds if you like)
        User::factory()
            ->count(10)
            ->state(['user_type' => UserType::STUDENT->value])
            ->create();
    }
}
