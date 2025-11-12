<?php

namespace Database\Factories;

use App\Enums\FacultyRoleTypeEnum;
use App\Models\Faculty;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory {
    protected $model = Faculty::class;

    public function definition(): array {
        return [
            'user_id' => User::factory()->faculty(),
            'department_id' => Department::factory(),
            'office' => $this->faker->bothify('IESB ###'),
            'role_type' => $this->faker->randomElement(FacultyRoleTypeEnum::class),
        ];
    }
}
