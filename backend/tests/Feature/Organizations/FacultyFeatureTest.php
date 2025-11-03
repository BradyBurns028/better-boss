<?php

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\DegreeProgram;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('faculty belongs to user and department; chairs exactly one department when assigned', function () {
    $dept = Department::factory()->create();
    $user = User::factory()->create();
    $faculty = Faculty::factory()->create(['user_id' => $user->id, 'department_id' => $dept->id]);

    $dept->update(['department_chair' => $faculty->id]);

    $faculty->load(['user','department','departments']);

    expect($faculty->user->id)->toBe($user->id)
        ->and($faculty->department->id)->toBe($dept->id)
        ->and(optional($faculty->departments)->id)->toBe($dept->id);
});

it('faculty degreePrograms() returns programs they chair; advisees() returns students assigned', function () {
    $dept = Department::factory()->create();
    $faculty = Faculty::factory()->create(['department_id' => $dept->id]);

    DegreeProgram::factory()->count(2)->create([
        'department_id' => $dept->id,
        'program_chair' => $faculty->id,
    ]);

    $students = Student::factory()->count(3)->create([
        'faculty_id' => $faculty->id,
    ]);

    $faculty->load(['degreePrograms','advisees']);

    expect($faculty->degreePrograms)->toHaveCount(2)
        ->and($faculty->advisees)->toHaveCount(3)
        ->and($faculty->advisees->pluck('id')->sort()->values()->all())
        ->toEqual($students->pluck('id')->sort()->values()->all());
});
