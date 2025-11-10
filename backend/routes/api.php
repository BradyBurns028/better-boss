<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DegreeProgramController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\PlannedCoursePivotController;
use App\Http\Controllers\PlanOfStudyController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('faculties', FacultyController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('degree_programs', DegreeProgramController::class);
    Route::apiResource('admins', AdminController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('course_sections', CourseSectionController::class);
    Route::apiResource('plans_of_study', PlanOfStudyController::class);
});

// all students in the authenticated faculty member's organization
Route::get('organization-students', [StudentController::class, 'organizationStudents'])
    ->middleware('auth:sanctum');