<?php

namespace Database\Seeders;

use App\Enums\FacultyRoleTypeEnum;
use App\Models\Admin;
use App\Models\DegreeProgram;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            OrganizationSeeder::class,
            CourseSeeder::class
        ]);
    }
}
