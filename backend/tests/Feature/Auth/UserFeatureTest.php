<?php

use App\Enums\UserType;
use App\Models\Admin;
use App\Models\DegreeProgram;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

function seedRolesFromUserTypeEnum(): void {
    // Clear cached Spatie data between tests
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    foreach (UserType::cases() as $case) {
        // Guard for multi-guards projects; adjust 'web' if you use another guard
        Role::findOrCreate($case->value, 'web');
    }
}

/**
 * Build the minimal graph needed for a valid DegreeProgram with program_chair.
 * Returns: [Department $dept, Faculty $chair, DegreeProgram $prog]
 */
function buildProgramWithChair(): array {
    $adminUser = User::factory()->create(['user_type' => UserType::ADMIN]);
    $admin = Admin::factory()->create(['user_id' => $adminUser->id]);
    $org = Organization::factory()->create(['admin_id' => $admin->id]);

    // department
    $dept = Department::factory()->create(['organization_id' => $org->id]);

    // chair faculty (must belong to the department)
    $chairUser = User::factory()->create(['user_type' => UserType::FACULTY]);
    $chair     = Faculty::factory()->create([
        'user_id'       => $chairUser->id,
        'department_id' => $dept->id,
    ]);

    // degree program with non-null program_chair
    $prog = DegreeProgram::factory()->create([
        'department_id' => $dept->id,
        'program_chair' => $chair->id,
    ]);

    return [$dept, $chair, $prog];
}

it('users table supports soft deletes', function () {
    $user = new User();

    expect(Schema::hasColumn($user->getTable(), 'deleted_at'))
        ->toBeTrue('users table must have deleted_at for SoftDeletes');
});

it('auto-assigns a role matching user_type on create', function () {
    seedRolesFromUserTypeEnum();

    /** @var User $u */
    $u = User::factory()->create([
        'user_type' => UserType::ADMIN,
    ])->fresh('roles');

    expect($u->roles)->not->toBeEmpty()
        ->and($u->hasRole(UserType::ADMIN->value))->toBeTrue();
});

it('assigns role only once (no duplicates)', function () {
    seedRolesFromUserTypeEnum();

    /** @var User $u */
    $u = User::factory()->create([
        'user_type' => UserType::FACULTY,
    ])->fresh('roles');

    // Spatie prevents duplicates at the DB/collection layer,
    // but we assert we only have one.
    expect($u->roles)->toHaveCount(1)
        ->and($u->hasRole(UserType::FACULTY->value))->toBeTrue();

    // Re-load to ensure nothing weird on refresh
    $u = $u->fresh('roles');
    expect($u->roles)->toHaveCount(1);
});

it('has one Admin via users.id = admins.user_id', function () {
    seedRolesFromUserTypeEnum();

    /** @var User $u */
    $u = User::factory()->create(['user_type' => UserType::ADMIN]);

    /** @var Admin $admin */
    $admin = Admin::factory()->create(['user_id' => $u->id]);

    $fresh = $u->fresh('admins');

    expect($fresh->admins)->not->toBeNull()
        ->and($fresh->admins->id)->toBe($admin->id);
});

it('has one Organization via owner_id', function () {
    seedRolesFromUserTypeEnum();

    /** @var User $owner */
    $owner = User::factory()->create(['user_type' => UserType::ADMIN]);
    // Need an Admin (unrelated) to satisfy Organization foreign key if required by your factory
    $adminUser = User::factory()->create(['user_type' => UserType::ADMIN]);
    $admin = Admin::factory()->create(['user_id' => $adminUser->id]);

    /** @var Organization $org */
    $org = Organization::factory()->create([
        'owner_id' => $owner->id,
        'admin_id' => $admin->id,
    ]);

    $fresh = $owner->fresh('organizations');

    expect($fresh->organizations)->not->toBeNull()
        ->and($fresh->organizations->id)->toBe($org->id);
});

it('has one Student via users.id = students.user_id', function () {
    seedRolesFromUserTypeEnum();

    /** @var User $u */
    $u = User::factory()->create(['user_type' => UserType::STUDENT]);

    [, $advisor, $prog] = buildProgramWithChair();

    /** @var Student $student */
    $student = Student::factory()->create([
        'user_id' => $u->id,
        'degree_program' => $prog->id,
        'faculty_id' => $advisor->id,
    ]);

    $fresh = $u->fresh('students');

    expect($fresh->students)->not->toBeNull()
        ->and($fresh->students->id)->toBe($student->id);
});

it('has one Faculty via users.id = faculties.user_id', function () {
    seedRolesFromUserTypeEnum();

    /** @var User $u */
    $u = User::factory()->create(['user_type' => UserType::FACULTY]);

    /** @var Faculty $faculty */
    $faculty = Faculty::factory()->create(['user_id' => $u->id]);

    $fresh = $u->fresh('faculties');

    expect($fresh->faculties)->not->toBeNull()
        ->and($fresh->faculties->id)->toBe($faculty->id);
});

it('hides sensitive attributes from array/json output', function () {
    /** @var User $u */
    $u = User::factory()->create([
        'password' => bcrypt('secret-123'),
        'remember_token' => Str::random(10),
    ]);

    $arr = $u->toArray();

    expect($arr)->not->toHaveKey('password')
        ->and($arr)->not->toHaveKey('remember_token');
});
