<?php

namespace Database\Factories\Courses;

use App\Models\Faculty;
use App\Models\Courses\Course;
use App\Models\Courses\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSectionFactory extends Factory {
    protected $model = CourseSection::class;

    public function definition(): array {
        $terms = ['Spring', 'Summer', 'Fall'];
        return [
            'course_id' => Course::factory(),
            'section_number' => $this->faker->numberBetween(1, 99),
            'term' => $this->faker->randomElement($terms),
            'year' => now()->year,
            'time' => $this->faker->time('H:i:s'),
            'instructor_id' => null,
            'capacity' => $this->faker->numberBetween(20, 60),
            'room_number' => strtoupper($this->faker->bothify('BLDG-###')),
        ];
    }

    public function forCourse(Course $course): self {
        return $this->state(fn() => ['course_id' => $course->id]);
    }

    public function withInstructor(Faculty $faculty): self {
        return $this->state(fn() => ['instructor_id' => $faculty->id]);
    }

    public function term(string $term, ?int $year = null): self {
        return $this->state(fn() => [
            'term' => $term,
            'year' => $year ?? now()->year,
        ]);
    }
}
