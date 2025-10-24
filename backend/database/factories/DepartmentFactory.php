<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory {
    protected $model = Department::class;

    public function definition(): array {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'department_chair' => null,
        ];
    }
}
