<?php

namespace Database\Seeders;

use App\Enums\FacultyRoleTypeEnum;
use App\Enums\UserType;
use App\Models\Admin;
use App\Models\DegreeProgram;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder {
    public function run(): void {
        $admins = Admin::all();

        $organizations = Organization::factory()
            ->count(5)
            ->recycle($admins)
            ->state([
                'owner_id' => null
            ])
            ->create();

        $organizations->each(function (Organization $org) {
            // 2a) Departments (3–5)
            /** @var Collection<int, Department> $departments */
            $departments = Department::factory()
                ->count(fake()->numberBetween(3, 5))
                ->for($org)
                ->create();

            /** @var array<int, Collection<int, Faculty>> $deptFaculty */
            $deptFaculty = [];
            foreach ($departments as $dept) {
                $deptFaculty[$dept->id] = collect();

                foreach (FacultyRoleTypeEnum::cases() as $role) {
                    $count = fake()->numberBetween(1, 3);
                    for ($i = 0; $i < $count; $i++) {
                        $facultyUser = User::factory()
                            ->faculty()
                            ->state(['email_verified_at' => now()])
                            ->create();

                        $fac = Faculty::factory()
                            ->for($facultyUser, 'user')
                            ->for($dept, 'department')
                            ->state([
                                // store enum value if your column is string; otherwise use $role directly
                                'role_type' => method_exists($role, 'value') ? $role->value : $role,
                            ])->create();

                        $deptFaculty[$dept->id]->push($fac);
                    }
                }
            }

            $usedChairIds = collect(); // track globally within the org
            foreach ($departments as $dept) {
                $candidates = ($deptFaculty[$dept->id] ?? collect())
                    ->whereNotIn('id', $usedChairIds)
                    ->values();

                // if no local candidates left, fall back to any unused faculty in org
                if ($candidates->isEmpty()) {
                    $allOrgFaculty = collect($deptFaculty)->flatten(1)->whereNotIn('id', $usedChairIds)->values();
                    $candidates = $allOrgFaculty;
                }

                if ($candidates->isNotEmpty()) {
                    $chair = $candidates->random();
                    $dept->update(['department_chair' => $chair->id]);
                    $usedChairIds->push($chair->id);
                } else {
                    $this->command?->warn("Dept {$dept->id} has no available faculty to assign as chair.");
                }
            }

            // 3) Degree programs (2–3 per dept) with a non-null program_chair from that dept
            /** @var Collection<int, DegreeProgram> $allPrograms */
            $allPrograms = collect();
            foreach ($departments as $dept) {
                $chairs = $deptFaculty[$dept->id]->pluck('id');

                // Guard: if somehow no faculty in this dept, skip programs here
                if ($chairs->isEmpty()) {
                    $this->command?->warn("Dept {$dept->id} has no faculty; skipping programs.");
                    continue;
                }

                $programs = DegreeProgram::factory()
                    ->count(fake()->numberBetween(2, 3))
                    ->for($dept)
                    ->state(fn () => [
                        'name'          => ucfirst(fake()->unique()->word()) . ' ' . strtoupper(fake()->bothify('##')),
                        'program_chair' => $chairs->random(),   // <-- satisfies NOT NULL
                    ])->create();

                $allPrograms = $allPrograms->merge($programs);
            }

            // 4) Students (15–20): choose a program, then pick a faculty from that program's department
            $studentCount = fake()->numberBetween(15, 20);
            if ($allPrograms->isEmpty()) {
                $this->command?->warn("Organization {$org->id} has no programs; skipping students.");
                return;
            }

            for ($i = 0; $i < $studentCount; $i++) {
                $program = $allPrograms->random();
                $deptId  = $program->department_id;

                // choose a faculty from the same department as the program
                $facForDept = $deptFaculty[$deptId] ?? collect();
                if ($facForDept->isEmpty()) {
                    // fallback: pick any faculty in org (should be rare)
                    $facForDept = collect($deptFaculty)->flatten();
                }
                $faculty = $facForDept->random();

                $studentUser = User::factory()
                    ->student()
                    ->state(['email_verified_at' => now()])
                    ->create();

                Student::create([
                    'degree_program' => $program->id,
                    'user_id'        => $studentUser->id,
                    'faculty_id'     => $faculty->id,
                ]);
            }
        });
    }
}