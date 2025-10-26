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

        $permissions = [
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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $roles = [
            'site admin' => Permission::all(),
            'administrator' => Permission::whereIn('name', [
                'view students',
                'view student details',
                'create students',
                'edit students',
                'delete students',
                'view faculty',
                'view faculty',
                'view faculty details',
                'create faculty',
                'edit faculty',
                'view departments',
                'create departments',
                'edit departments',
                'delete departments',
                'index organizations',
                'index organization details',
                'view degree programs',
                'create degree programs',
                'edit degree programs',
                'delete degree programs',
                'view degree requirements',
                'edit degree requirements',
                'view courses',
                'create courses',
                'edit courses',
                'delete courses',
                'view course sections',
                'create course sections',
                'edit course sections',
                'delete course sections'
            ])->get(),
            'staff' => Permission::whereIn('name', [
                'view facutly',
                'view departments',
                'index organizations'
            ])->get(),
            'instructor' => Permission::whereIn('name', [
                'view adviesees',
                'view faculty',
                'view departments',
                'index organizations',
                'view degree programs',
                'view degree requirements',
                'view courses',
                'view course sections',
                'view enrolled students',
                'index plans of study'
            ])->get(),
            'student' => Permission::whereIn('name', [
                'view instructors',
                'view departments',
                'index organizations',
                'view degree programs',
                'view degree requirements',
                'view courses',
                'view course sections',
                'index plans of study',
                'create plans of study',
                'edit plans of study',
                'delete plans of study'
            ])->get()
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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::whereIn('name', [
            'site admin',
            'administrator',
            'staff',
            'instructor',
            'student'
        ])->delete();

        Permission::whereIn('name', [
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
        ])->delete();
    }
};
