<?php

use App\Models\Admin;
use App\Models\Department;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('organizations table supports SoftDeletes', function () {
    $this->assertTrue(
        Schema::hasColumn((new Organization())->getTable(), 'deleted_at')
    );
});

it('organization belongs to an admin and an owner user; has many departments', function () {
    $adminUser = User::factory()->create();
    $admin = Admin::factory()->create(['user_id' => $adminUser->id]);

    $owner = User::factory()->create();

    $org = Organization::factory()->create([
        'admin_id' => $admin->id,
        'owner_id' => $owner->id,
    ]);

    Department::factory()->count(3)->create(['organization_id' => $org->id]);

    $org->load(['admin.user', 'user', 'departments']);

    expect($org->admin->id)->toBe($admin->id)
        ->and($org->admin->user->id)->toBe($adminUser->id)
        ->and($org->user->id)->toBe($owner->id)
        ->and($org->departments)->toHaveCount(3);
});

it('soft deleting an organization keeps departments intact (no hard delete/cascade)', function () {
    $org = Organization::factory()->create();
    $dept = Department::factory()->create(['organization_id' => $org->id]);

    $org->delete();

    expect(Department::find($dept->id))->not->toBeNull();

    expect(Organization::find($org->id))->toBeNull()
        ->and(Organization::withTrashed()->find($org->id))->not->toBeNull();

    $linkedOrg = $dept->organization()->withTrashed()->first();
    expect($linkedOrg?->id)->toBe($org->id);
});
