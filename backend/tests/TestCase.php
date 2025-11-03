<?php

namespace Tests;

use App\Enums\UserType;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase {
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Make sure Spatie permission cache is clear for each test run
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create the roles expected by your User model/factory
        // If you have App\Enums\UserType, use that. Otherwise list them.
        if (enum_exists(UserType::class)) {
            foreach (UserType::cases() as $case) {
                Role::firstOrCreate([
                    'name'       => $case->value,
                    'guard_name' => 'web',
                ]);
            }
        } else {
            foreach (['admin','faculty','student'] as $name) {
                Role::firstOrCreate([
                    'name'       => $name,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
