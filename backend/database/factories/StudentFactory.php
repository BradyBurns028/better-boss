<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\DegreeProgram;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory {
    public function definition(): array {
        return [
            'user_id' => User::factory()->state(['user_type' => UserType::STUDENT]),
            'faculty_id' => Faculty::factory(),
            'degree_program' => DegreeProgram::factory(),
        ];
    }
}
