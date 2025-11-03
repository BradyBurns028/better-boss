<?php

namespace Database\Factories\Courses;

use App\Models\Courses\Course;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory {
    protected $model = Course::class;

    public function definition(): array {
        $prefix = strtoupper($this->faker->bothify('????'));
        $num = $this->faker->numberBetween(100, 499);

        return [
            'course_code' => "{$prefix}-{$num}",
            'name' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->optional(0.6)->paragraph(),
            'credits' => $this->faker->numberBetween(1, 5),
            'department_id' => Department::factory(),
            'prerequisite_id'=> null,
        ];
    }

    public function forDepartment(Department $dept): self {
        return $this->state(fn () => ['department_id' => $dept->id]);
    }
}
