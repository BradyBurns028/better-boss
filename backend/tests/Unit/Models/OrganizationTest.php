<?php

use App\Models\Admin;
use App\Models\Department;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('has fillable attributes and uses soft deletes', function () {
    $org = new Organization();

    expect($org->getFillable())
        ->toEqualCanonicalizing(['name','admin_id','owner_id','address']);

    // SoftDeletes column exists
    $this->assertTrue(
        Schema::hasColumn($org->getTable(), 'deleted_at'),
        'organizations table must have deleted_at for SoftDeletes'
    );
});

it('belongs to an admin', function () {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Admin $admin */
    $admin = Admin::factory()->create(['user_id' => $user->id]);
    /** @var Organization $org */
    $org = Organization::factory()->create(['admin_id' => $admin->id]);

    expect($org->admin)->not->toBeNull()
        ->and($org->admin->id)->toBe($admin->id);
});

it('belongs to a user as owner via owner_id', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var User $adminUser */
    $adminUser = User::factory()->create();
    /** @var Admin $admin */
    $admin = Admin::factory()->create(['user_id' => $adminUser->id]);

    /** @var Organization $org */
    $org = Organization::factory()->create([
        'admin_id' => $admin->id,
        'owner_id' => $owner->id,
    ]);

    expect($org->user)->not->toBeNull()
        ->and($org->user->id)->toBe($owner->id);
});

it('has many departments', function () {
    /** @var User $adminUser */
    $adminUser = User::factory()->create();
    /** @var Admin $admin */
    $admin = Admin::factory()->create(['user_id' => $adminUser->id]);
    /** @var Organization $org */
    $org = Organization::factory()->create(['admin_id' => $admin->id]);

    Department::factory()->count(3)->create(['organization_id' => $org->id]);

    expect($org->departments)->toHaveCount(3);
});
