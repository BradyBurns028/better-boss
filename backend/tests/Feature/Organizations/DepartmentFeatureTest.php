<?php

use App\Models\Admin;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Organization;
use App\Models\User;
use App\Models\DegreeProgram;
use App\Models\Courses\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('department belongs to an organization', function () {
    $admin = Admin::factory()->create(['user_id' => User::factory()->create()->id]);
    $org = Organization::factory()->create(['admin_id' => $admin->id]);

    $dept = Department::factory()->create(['organization_id' => $org->id]);

    expect($dept->organization->id)->toBe($org->id);
});

it('department chair can be assigned and changed', function () {
    $org = Organization::factory()->create();
    $dept = Department::factory()->create(['organization_id' => $org->id]);

    $chairUserA = User::factory()->create();
    $chairA = Faculty::factory()->create(['user_id' => $chairUserA->id, 'department_id' => $dept->id]);

    $dept->update(['department_chair' => $chairA->id]);
    $dept->refresh()->load('departmentChair');

    expect($dept->departmentChair->id)->toBe($chairA->id);

    $chairUserB = User::factory()->create();
    $chairB = Faculty::factory()->create(['user_id' => $chairUserB->id, 'department_id' => $dept->id]);

    $dept->update(['department_chair' => $chairB->id]);
    $dept->refresh()->load('departmentChair');

    expect($dept->departmentChair->id)->toBe($chairB->id);
});

it('department has many degree programs, faculty, and courses', function () {
    $org = Organization::factory()->create();
    $dept = Department::factory()->create(['organization_id' => $org->id]);

    DegreeProgram::factory()->count(2)->create(['department_id' => $dept->id]);
    Faculty::factory()->count(3)->create(['department_id' => $dept->id]);
    Course::factory()->count(4)->create(['department_id' => $dept->id]);

    $dept->load(['degreePrograms','faculty','courses']);

    expect($dept->degreePrograms)->toHaveCount(2)
        ->and($dept->faculty)->toHaveCount(3)
        ->and($dept->courses)->toHaveCount(4);
});
