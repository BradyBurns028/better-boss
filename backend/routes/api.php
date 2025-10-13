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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('students', StudentController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('organizations', OrganizationController::class);
Route::apiResource('faculties', FacultyController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('degree_programs', DegreeProgramController::class);
Route::apiResource('admins', AdminController::class);

// Route::middleware('auth:sanctum')->get('/users', function () {
//     return User::all();
// });

// Route::middleware('auth:sanctum')->get('/', function () {

// });