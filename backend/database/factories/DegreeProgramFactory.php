<?php

namespace Database\Factories;

use App\Models\DegreeProgram;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class DegreeProgramFactory extends Factory {
    protected $model = DegreeProgram::class;

    private static array $majors = [
        'Electrical Engineering',
        'Computer Engineering',
        'Computer Science',
        'Mechanical Engineering',
        'Civil Engineering',
        'Biomedical Engineering',
        'Industrial Engineering',
        'Aerospace Engineering',
        'Software Engineering',
        'Data Science',
        'Information Systems',
        'Materials Science',
        'Environmental Engineering',
        'Chemical Engineering',
        'Applied Mathematics',
        'Physics',
        'Chemistry',
        'Biology',
        'Economics',
        'Finance',
        'Accounting',
        'Marketing',
        'Operations Research',
    ];

    public function definition(): array {
        $department = Department::factory()->create();

        $chair = Faculty::factory()->create([
            'department_id' => $department->id,
        ]);

        return [
            'department_id' => Department::factory(),
            'name' => $this->faker->randomElement(self::$majors),
            'program_chair' => $chair->id,
        ];
    }

    /**
     * Helper to bind the program to a given department and keep chair aligned.
     */
    public function forDepartment(Department $department): self {
        return $this->state(function (array $attrs) use ($department) {
            $chair = Faculty::factory()->create([
                'department_id' => $department->id,
            ]);

            $major = collect(self::$majors)
                ->shuffle()
                ->first(fn ($m) => mb_strtolower($m) !== mb_strtolower($department->name))
                ?? $this->faker->unique()->jobTitle();

            return [
                'department_id' => $department->id,
                'name' => $major,
                'program_chair' => $chair->id,
            ];
        });
    }
}
