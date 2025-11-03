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
            // All users
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'index_users',
            'index_user_details',

            // Students
            'view_students',
            'view_student_details',
            'create_students',
            'edit_students',
            'delete_students',
            'view_advisees',

            // Faculty
            'view_faculty',
            'view_faculty_details',
            'create_faculty',
            'edit_faculty',
            'view_administrators',
            'view_instructors',
            'view_staff',
            'delete_faculty',

            // Departments
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',

            // Organizations
            'view_organizations',
            'create_organizations',
            'edit_organizations',
            'delete_organizations',
            'index_organizations',
            'index_organization_details',

            // Degree Programs
            'view_degree_programs',
            'create_degree_programs',
            'edit_degree_programs',
            'delete_degree_programs',

            // Degree Requirements
            'view_degree_requirements',
            'edit_degree_requirements',

            // Courses
            'view_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',

            // Course Sections
            'view_course_sections',
            'create_course_sections',
            'edit_course_sections',
            'delete_course_sections',
            'view_enrolled_students',

            // Plans of Study
            'view_plans_of_study',
            'create_plans_of_study',
            'edit_plans_of_study',
            'delete_plans_of_study',
            'index_plans_of_study',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $roles = [
            'site_admin' => Permission::all(),
            'administrator' => Permission::whereIn('name', [
                'view_students',
                'view_student_details',
                'create_students',
                'edit_students',
                'delete_students',
                'view_faculty',
                'view_faculty_details',
                'create_faculty',
                'edit_faculty',
                'delete_faculty',
                'view_departments',
                'create_departments',
                'edit_departments',
                'delete_departments',
                'index_organizations',
                'index_organization_details',
                'view_degree_programs',
                'create_degree_programs',
                'edit_degree_programs',
                'delete_degree_programs',
                'view_degree_requirements',
                'edit_degree_requirements',
                'view_courses',
                'create_courses',
                'edit_courses',
                'delete_courses',
                'view_course_sections',
                'create_course_sections',
                'edit_course_sections',
                'delete_course_sections'
            ])->get(),
            'staff' => Permission::whereIn('name', [
                'view_faculty',
                'view_departments',
                'index_organizations'
            ])->get(),
            'instructor' => Permission::whereIn('name', [
                'view_advisees',
                'view_faculty',
                'view_departments',
                'index_organizations',
                'view_degree_programs',
                'view_degree_requirements',
                'view_courses',
                'view_course_sections',
                'view_enrolled_students',
                'view_plans_of_study'
            ])->get(),
            'student' => Permission::whereIn('name', [
                'view_instructors',
                'view_departments',
                'index_organizations',
                'view_degree_programs',
                'view_degree_requirements',
                'view_courses',
                'view_course_sections',
                'index_plans_of_study',
                'create_plans_of_study',
                'edit_plans_of_study',
                'delete_plans_of_study'
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
            'site_admin',
            'administrator',
            'staff',
            'instructor',
            'student'
        ])->delete();

        Permission::whereIn('name', [
            // All users
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'index_users',
            'index_user_details',

            // Students
            'view_students',
            'view_student_details',
            'create_students',
            'edit_students',
            'delete_students',
            'view_advisees',

            // Faculty
            'view_faculty',
            'view_faculty_details',
            'create_faculty',
            'edit_faculty',
            'view_administrators',
            'view_instructors',
            'view_staff',
            'delete_faculty',

            // Departments
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',

            // Organizations
            'view_organizations',
            'create_organizations',
            'edit_organizations',
            'delete_organizations',
            'index_organizations',
            'index_organization_details',

            // Degree Programs
            'view_degree_programs',
            'create_degree_programs',
            'edit_degree_programs',
            'delete_degree_programs',

            // Degree Requirements
            'view_degree_requirements',
            'edit_degree_requirements',

            // Courses
            'view_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',

            // Course Sections
            'view_course_sections',
            'create_course_sections',
            'edit_course_sections',
            'delete_course_sections',
            'view_enrolled_students',

            // Plans of Study
            'view_plans_of_study',
            'create_plans_of_study',
            'edit_plans_of_study',
            'delete_plans_of_study',
            'index_plans_of_study',
        ])->delete();
    }
};
