<?php

use App\Models\Admin;
use App\Models\Department;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('has fillable attributes and uses soft deletes', function () {
    $department = new Department();

    expect($department->getFillable())
        ->toEqualCanonicalizing(['name','organization_id','department_chair']);

    $this->assertTrue(
        Schema::hasColumn($department->getTable(), 'deleted_at'),
        'departments table must have deleted_at for SoftDeletes'
    );
});

it('belongs to an organization', function () {
    $user = User::factory()->create();
    $admin = Admin::factory()->create(['user_id' => $user->id]);
    $org = Organization::factory()->create(['admin_id' => $admin->id]);

    expect($org->admin)->not->toBeNull()
        ->and($org->admin->id)->toBe($admin->id);
});

it('belongs to a user as owner via owner_id', function () {
    $owner = User::factory()->create();
    $admin = Admin::factory()->create(['user_id' => User::factory()->create()->id]);

    $org = Organization::factory()->create([
        'admin_id' => $admin->id,
        'owner_id' => $owner->id,
    ]);

    expect($org->user)->not->toBeNull()
        ->and($org->user->id)->toBe($owner->id);
});

it('has many departments', function () {
    $adminUser = User::factory()->create();
    $admin = Admin::factory()->create(['user_id' => $adminUser->id]);
    $org = Organization::factory()->create(['admin_id' => $admin->id]);

    Department::factory()->count(3)->create(['organization_id' => $org->id]);

    expect($org->departments)->toHaveCount(3);
});
