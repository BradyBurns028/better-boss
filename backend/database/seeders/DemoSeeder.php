<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use App\Models\Admin;
use App\Models\Organization;
use App\Models\Department;
use App\Models\DegreeProgram;
use App\Models\Faculty;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUniversitySeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // --- 1) Admin + User -------------------------------------------------
        $adminUser = User::query()->create([
            'first_name'        => 'System',
            'last_name'         => 'Admin',
            'email'             => 'admin@example.com',
            'password'          => Hash::make('password'), // change in prod
            'user_type'         => UserType::ADMIN->value,
            'email_verified_at' => now(),
            'remember_token'    => null,
        ]);

        $admin = Admin::query()->create([
            'user_id' => $adminUser->id,
        ]);

        // --- 2) Organization --------------------------------------------------
        $org = Organization::query()->create([
            'name'      => 'Example University',
            'admin_id'  => $admin->id,
            'owner_id'  => $adminUser->id, // optional: can be null
            'address'   => '100 College Ave, Sample City, ST 00000',
        ]);

        // --- 3) Department (chair set later after faculty exist) --------------
        $dept = Department::query()->create([
            'name'             => 'Computer Science',
            'organization_id'  => $org->id,
            'department_chair' => null, // will update after faculty creation
        ]);

        // --- 4) Faculty (3 users + faculty rows) ------------------------------
        $facultyRoles = ['Professor', 'Associate Professor', 'Assistant Professor'];
        $facultyRecords = [];

        for ($i = 0; $i < 3; $i++) {
            $user = User::query()->create([
                'first_name'        => $faker->firstName(),
                'last_name'         => $faker->lastName(),
                'email'             => $faker->unique()->safeEmail(),
                'password'          => Hash::make('password'),
                'user_type'         => UserType::FACULTY->value,
                'email_verified_at' => now(),
                'remember_token'    => null,
            ]);

            $faculty = Faculty::query()->create([
                'user_id'       => $user->id,
                'department_id' => $dept->id,
                'office'        => 'Room ' . $faker->numberBetween(200, 499),
                'role_type'     => $facultyRoles[$i],
            ]);

            $facultyRecords[] = $faculty;
        }

        // Pick a chair (first faculty) and update the department
        $chair = $facultyRecords[0];
        $dept->update(['department_chair' => $chair->id]);

        // --- 5) Degree Program (program_chair is a faculty FK) ----------------
        $program = DegreeProgram::query()->create([
            'name'          => 'B.S. in Computer Science',
            'department_id' => $dept->id,
            'program_chair' => $chair->id,
        ]);

        // --- 6) Students (10 users + student rows) ----------------------------
        for ($i = 0; $i < 10; $i++) {
            $user = User::query()->create([
                'first_name'        => $faker->firstName(),
                'last_name'         => $faker->lastName(),
                'email'             => $faker->unique()->safeEmail(),
                'password'          => Hash::make('password'),
                'user_type'         => UserType::STUDENT->value,
                'email_verified_at' => now(),
                'remember_token'    => null,
            ]);

            // Randomly assign a faculty advisor among the 3
            $advisor = $faker->randomElement($facultyRecords);

            // NOTE: Your schema names the FK column as 'degree_program' (no _id).
            Student::query()->create([
                'user_id'        => $user->id,
                'faculty_id'     => $advisor->id,
                'degree_program' => $program->id,
            ]);
        }
    }
}
