<?php

namespace Database\Factories;

use App\Models\DegreeProgram;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DegreeProgramFactory extends Factory {
    protected $model = DegreeProgram::class;

    public function definition(): array {
        return [
            'department_id' => Department::factory(),
            'name' => $this->faker->randomElement(['B.S.', 'M.S.', 'Ph.D.']),
        ];
    }
}
