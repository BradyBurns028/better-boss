<?php

use App\Models\Department;
use App\Models\DegreeProgram;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('degree program ties back to its organization via department', function () {
    $org = Organization::factory()->create();
    $dept = Department::factory()->create(['organization_id' => $org->id]);
    $prog = DegreeProgram::factory()->create([
        'department_id' => $dept->id,
        'program_chair' => 1
    ]);

    $prog->load('department.organization');

    expect($prog->department->organization->id)->toBe($org->id);
});

it('student->organization accessor resolves organization through program -> department', function () {
    $org  = Organization::factory()->create();
    $dept = Department::factory()->create(['organization_id' => $org->id]);
    $prog = DegreeProgram::factory()->create(['department_id' => $dept->id]);

    $studentUser = User::factory()->create();
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'degree_program' => $prog->id,
    ]);

    expect($student->organization?->id)->toBe($org->id);
});
