<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissons = [
            //All users
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            'index users',
            'index user details',

            //Students
            'view students',
            'view student details',
            'create students',
            'edit students',
            'delete students',
            'view advisees',

            //Faculty
            'view faculty',
            'view faculty details',
            'create faculty',
            'edit faculty',
            'view administrators',
            'view instructors',
            'view staff',

            //Departments
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',

            //Organizations
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',
            'index organizations',
            'index organization details',

            //Degree Programs
            'view degree programs',
            'create degree programs',
            'edit degree programs',
            'delete degree programs',

            //Degree Requirements
            'view degree requirements',
            'edit degree requirements',

            //Courses
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',

            //Course Sections
            'view course sections',
            'create course sections',
            'edit course sections',
            'delete course sections',
            'view enrolled students',

            //Plans of Study
            'view plans of study',
            'create plans of study',
            'edit plans of study',
            'delete plans of study',
            'index plans of study',
        ];

        foreach ($permissons as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $roles = [
            'site admin' => Permission::all(),
            'administrator' => Permission::whereIn('name', [])->get(),
            'faculty' => Permission::whereIn('name', [])->get(),
            'staff' => Permission::whereIn('name', [])->get(),
            'instructor' => Permission::whereIn('name', [])->get(),
            'student' => Permission::whereIn('name', [])->get()
        ];

        foreach ($roles as $role => $permissions) {
            $roleModel = Role::firstOrCreate(['name' => $role]);
            $roleModel->syncPermissions($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
