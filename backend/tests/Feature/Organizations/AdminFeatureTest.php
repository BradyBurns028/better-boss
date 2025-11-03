<?php

use App\Models\Admin;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin belongs to a user and has many organizations', function () {
    $user = User::factory()->create();
    $admin = Admin::factory()->create(['user_id' => $user->id]);

    Organization::factory()->count(2)->create(['admin_id' => $admin->id]);

    $admin->load(['user','organizations']);

    expect($admin->user->id)->toBe($user->id)
        ->and($admin->organizations)->toHaveCount(2);
});
