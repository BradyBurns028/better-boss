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
        ]);
        $users = User::factory(10)->create();

        $adminUsers = $users->where('user_type', UserType::ADMIN->value)->values();
        $facultyUsers = $users->where('user_type', UserType::FACULTY->value)->values();
        $studentUsers = $users->where('user_type', UserType::STUDENT->value)->values();

        if ($adminUsers->isEmpty()) {
            $fallbackAdminUser = User::create([
                'first_name' => 'System',
                'last_name'  => 'Admin',
                'email'      => 'admin@example.com',
                'password'   => Hash::make('password'),
                'user_type'  => UserType::ADMIN->value,
                'email_verified_at' => now(),
            ]);
            $adminUsers = collect([$fallbackAdminUser]);
        }
        if ($facultyUsers->count() < 2) {
            throw new \RuntimeException(
                'Seeder needs at least 2 faculty users to assign unique chairs. ' .
                'Please adjust your factory distribution or reduce the number of departments/programs.'
            );
        }
        $admins = $adminUsers->map(function (User $u) {
            return Admin::create(['user_id' => $u->id]);
        })->values();

        $orgOwnerUser = $facultyUsers->first() ?? $adminUsers->first();
        $organization = Organization::create([
            'name'     => 'Louisiana Tech University',
            'admin_id' => $admins->first()->id,
            'owner_id' => $orgOwnerUser->id,
            'address'  => '123 Drive Lane Ruston, LA 71272',
        ]);

        $departmentNames = ['Computer Science', 'Mathematics'];
        $departments = collect($departmentNames)->map(function ($name) use ($organization) {
            return Department::create([
                'name'             => $name,
                'organization_id'  => $organization->id,
                'department_chair' => null,
            ]);
        });

        $faculties = collect();
        $roles = FacultyRoleTypeEnum::cases();

        foreach ($facultyUsers as $idx => $user) {
            $dept = $departments[$idx % $departments->count()];
            $faculties->push(
                Faculty::create([
                    'user_id'       => $user->id,
                    'department_id' => $dept->id,
                    'office'        => 'IESB ' . (200 + $user->id),
                    'role_type'     => $roles[$idx % count($roles)],
                ])
            );
        }

        if ($faculties->count() < $departments->count()) {
            throw new \RuntimeException('Not enough faculty to assign unique department chairs.');
        }

        $chairPool = $faculties->values();
        $usedChairIds = [];
        foreach ($departments as $dept) {
            $chair = $chairPool->shift();
            $usedChairIds[] = $chair->id;
            $dept->update(['department_chair' => $chair->id]);
        }

        $remainingFaculty = $faculties->whereNotIn('id', $usedChairIds)->values();

        // Create degree programs and CAPTURE them
        $degreeProgramNames = ['B.S. in Computer Science', 'B.S. in Mathematics'];
        $degreePrograms = collect(); // <-- ADD
        foreach ($departments as $i => $dept) {
            $candidate = $remainingFaculty->shift();

            if (!$candidate) {
                $candidate = $faculties
                    ->where('department_id', $dept->id)
                    ->where('id', '!=', $dept->department_chair)
                    ->first();
            }

            if (!$candidate) {
                $candidate = $faculties->firstWhere('id', '!=', $dept->department_chair);
            }

            $degreePrograms->push( // <-- CAPTURE
                DegreeProgram::create([
                    'name'          => $degreeProgramNames[$i],
                    'department_id' => $dept->id,
                    'program_chair' => $candidate->id,
                ])
            );
        }

        foreach ($studentUsers as $studentUser) {
            $program = $degreePrograms->random();

            $advisor = $faculties
                ->where('department_id', $program->department_id)
                ->random() ?? $faculties->random();

            Student::create([
                'user_id'        => $studentUser->id,
                'faculty_id'     => $advisor->id,
                'degree_program' => $program->id,
            ]);
        }
    }
}
