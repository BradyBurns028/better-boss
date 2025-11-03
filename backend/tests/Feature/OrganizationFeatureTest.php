<?php

use App\Models\Admin;
use App\Models\Department;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helpers
 */
function makeAdminUser(): array {
    $user = User::factory()->create(['user_type' => 'admin']);
    $admin = Admin::factory()->create(['user_id' => $user->id]);
    return [$user, $admin];
}

it('stores an organization with valid data', function () {
    [$user, $admin] = makeAdminUser();

    $payload = [
        'name' => 'Test University',
        'admin_id' => $admin->id,
        'owner_id' => $user->id,
        'address' => '100 Test Ave',
    ];

    $res = $this->postJson('/api/organizations', $payload);

    $res->assertOk()
        ->assertJsonFragment([
            'name' => 'Test University',
            'admin_id' => $admin->id,
            'owner_id' => $user->id,
            'address' => '100 Test Ave',
        ]);

    $this->assertDatabaseHas('organizations', [
        'name' => 'Test University',
        'admin_id' => $admin->id,
        'owner_id' => $user->id,
    ]);
});

it('validates required fields when storing', function () {
    // Missing name and admin_id
    $res = $this->postJson('/api/organizations', [
        'address' => 'Nope',
    ]);

    $res->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'admin_id']);
});

it('rejects nonexistent admin on store', function () {
    $user = User::factory()->create();

    $res = $this->postJson('/api/organizations', [
        'name'     => 'Bad Admin Org',
        'admin_id' => 999999,    // does not exist in admins table
        'owner_id' => $user->id,
    ]);

    $res->assertStatus(422)
        ->assertJsonValidationErrors(['admin_id']);
});

it('shows an organization with admin, user, and departments loaded', function () {
    [$ownerUser, $admin] = makeAdminUser();

    /** @var Organization $org */
    $org = Organization::factory()->create([
        'admin_id' => $admin->id,
        'owner_id' => $ownerUser->id,
    ]);

    // Attach a couple of departments
    Department::factory()->count(2)->create(['organization_id' => $org->id]);

    $res = $this->getJson("/api/organizations/{$org->id}");

    // Controller uses OrganizationResource::make($organization) with ->load('admin','user','departments')
    $res->assertOk()
        ->assertJsonPath('data.id', $org->id)
        ->assertJsonPath('data.admin.id', $admin->id)
        ->assertJsonPath('data.user.id', $ownerUser->id)
        ->assertJsonCount(2, 'data.departments');
});

it('updates an organization partially (name + null owner)', function () {
    [$ownerUser, $admin] = makeAdminUser();

    /** @var Organization $org */
    $org = Organization::factory()->create([
        'name'     => 'Old Name',
        'admin_id' => $admin->id,
        'owner_id' => $ownerUser->id,
        'address'  => 'Old Address',
    ]);

    $res = $this->putJson("/api/organizations/{$org->id}", [
        'name'     => 'New Name',
        'owner_id' => null, // explicitly clear
    ]);

    $res->assertOk()
        ->assertJsonFragment([
            'id'       => $org->id,
            'name'     => 'New Name',
            'owner_id' => null,
        ]);

    $this->assertDatabaseHas('organizations', [
        'id'       => $org->id,
        'name'     => 'New Name',
        'owner_id' => null,
    ]);

    // unchanged
    $this->assertDatabaseHas('organizations', [
        'id'       => $org->id,
        'admin_id' => $admin->id,
        'address'  => 'Old Address',
    ]);
});

it('validates on update (bad admin_id)', function () {
    [$ownerUser, $admin] = makeAdminUser();
    $org = Organization::factory()->create([
        'admin_id' => $admin->id,
        'owner_id' => $ownerUser->id,
    ]);

    $res = $this->putJson("/api/organizations/{$org->id}", [
        'admin_id' => 123456789, // not in admins table
    ]);

    $res->assertStatus(422)->assertJsonValidationErrors(['admin_id']);
});
