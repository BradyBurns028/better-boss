<?php

namespace Database\Seeders;

use App\Models\Courses\Course;
use App\Models\DegreeProgram;
use App\Models\Department;
use App\Models\Faculty;
use Database\Factories\Courses\CourseFactory;
use Database\Factories\Courses\CourseSectionFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CourseSeeder extends Seeder {
    public function run(): void {
        /** @var Collection<int, Department> $departments */
        $departments = Department::query()->with(['organization'])->get();

        if ($departments->isEmpty()) {
            $this->command?->warn('CourseSeeder: No departments found. Seed organizations/departments first.');
            return;
        }

        // Preload faculty & programs per department to avoid N+1
        $facultyByDept = Faculty::query()->get()->groupBy('department_id');
        $programsByDept = DegreeProgram::query()->get()->groupBy('department_id');

        foreach ($departments as $dept) {
            // 1) Courses per department
            /** @var Collection<int, Course> $courses */
            $courses = CourseFactory::new()
                ->count(fake()->numberBetween(6, 10))
                ->forDepartment($dept)
                ->create();

            // 2) Prerequisites (no cycles): later courses may depend on an earlier one
            $sorted = $courses->values();
            for ($i = 1; $i < $sorted->count(); $i++) {
                if (fake()->boolean(45)) { // ~45% get a prereq
                    $prereq = $sorted->slice(0, $i)->random();
                    $course = $sorted->get($i);
                    // ensure no self-ref
                    if ($prereq->id !== $course->id) {
                        $course->update(['prerequisite_id' => $prereq->id]);
                    }
                }
            }

            // 3) Sections per course (1–3), try to assign instructor from this department
            $deptFaculty = $facultyByDept->get($dept->id, collect());

            foreach ($courses as $course) {
                $sectionsToMake = fake()->numberBetween(1, 3);

                $available = collect(range(1, 99))->shuffle()->take($sectionsToMake);

                $facultyPool = $deptFaculty->isNotEmpty() ? $deptFaculty : $facultyByDept->flatten(1);
                if ($facultyPool->isEmpty()) {
                    $this->command?->warn("CourseSeeder: no faculty; skipping sections for course {$course->id}.");
                    continue;
                }

                foreach ($available as $secNo) {
                    $instructorId = $facultyPool->random()->id;

                    CourseSectionFactory::new()
                        ->forCourse($course)
                        ->term(fake()->randomElement(['Spring','Summer','Fall']), now()->year)
                        ->state([
                            'section_number' => $secNo,
                            'instructor_id'  => $instructorId,
                        ])
                        ->create();
                }
            }

            $programs = $programsByDept->get($dept->id, collect());
            if ($programs->isNotEmpty()) {
                $attachCourses = $courses->shuffle()->take(fake()->numberBetween(3, min(6, $courses->count())));
                foreach ($programs as $program) {
                    foreach ($attachCourses as $c) {
                        if (!$program->courses()->where('course_id', $c->id)->exists()) {
                            $program->courses()->attach($c->id, [
                                'course_set' => null,
                                'minimum_grade' => fake()->randomElement([60, 70]),
                            ]);
                        }
                    }
                }
            }
        }
    }
}