<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory {
    protected $model = Organization::class;

    public function definition(): array {
        return [
            'name' => $this->faker->company() . ' University',
            'admin_id' => Admin::factory(),
            'owner_id' => User::factory()->admin(),
            'address'  => $this->faker->address(),
        ];
    }
}
