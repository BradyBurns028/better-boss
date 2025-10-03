<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new /**
 * Migration class to create tables for user types and their relationships.
 */ class extends Migration
{
    /**
     * Run the migrations.
     * This method creates the necessary tables for the user types and their relationships.
     */
    public function up(): void
    {
        // Create the 'admins' table
        Schema::create('admins', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained('users'); // Foreign key to 'users' table
            $table->timestamps(); // Timestamps for created_at and updated_at
        });

        // Create the 'organizations' table
        Schema::create('organizations', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Organization name
            $table->foreignId('admin_id')->constrained('admin'); // Foreign key to 'admins' table
            $table->foreignId('owner_id')->nullable()->constrained('users'); // Nullable foreign key to 'users' table
            $table->string('address'); // Organization address
            $table->timestamps(); // Timestamps for created_at and updated_at
            $table->softDeletes(); // Soft delete column
        });

        // Create the 'faculties' table
        Schema::create('faculties', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->timestamps(); // Timestamps for created_at and updated_at
            $table->foreignId('user_id')->constrained('users'); // Foreign key to 'users' table
            $table->string('office')->nullable(); // Nullable office location
            $table->string('role_type'); // Role type of the faculty
        });

        // Create the 'departments' table
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Department name
            $table->foreignId('organization_id')->constrained('organizations'); // Foreign key to 'organizations' table
            $table->timestamps(); // Timestamps for created_at and updated_at
            $table->softDeletes(); // Soft delete column
        });

        // Create the 'degree_programs' table
        Schema::create('degree_programs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Degree program name
            $table->foreignId('department_id')->constrained('departments'); // Foreign key to 'departments' table
            $table->foreignId('program_chair')->constrained('faculty'); // Foreign key to 'faculties' table
            $table->timestamps(); // Timestamps for created_at and updated_at
        });

        // Create the 'students' table
        Schema::create('students', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->timestamps(); // Timestamps for created_at and updated_at
            $table->ForeignId('degree_program')->constrained('degree_program'); // Foreign key to 'degree_programs' table
            $table->ForeignId('user_id')->constrained('users'); // Foreign key to 'users' table
            $table->ForeignId('faculty_id')->constrained('faculty'); // Foreign key to 'faculties' table
        });

        // Add a foreign key to the 'faculties' table for the 'departments' table
        Schema::table('faculties', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained('departments'); // Foreign key to 'departments' table
        });

        // Add a foreign key to the 'departments' table for the department chair in the 'faculties' table
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('department_chair')->constrained('faculty'); // Foreign key to 'faculties' table
        });
    }

    /**
     * Reverse the migrations.
     * This method drops the tables and columns created in the `up` method.
     */
    public function down(): void
    {
        // Remove the foreign key and column for department chair in 'departments' table
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['department_chair']);
            $table->dropColumn('department_chair');
        });

        // Remove the foreign key and column for department in 'faculties' table
        Schema::table('faculties', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        // Drop the 'students' table
        Schema::dropIfExists('students');

        // Drop the 'degree_programs' table
        Schema::dropIfExists('degree_programs');

        // Drop the 'departments' table
        Schema::dropIfExists('departments');

        // Drop the 'faculties' table
        Schema::dropIfExists('faculties');

        // Drop the 'organizations' table
        Schema::dropIfExists('organizations');

        // Drop the 'admins' table
        Schema::dropIfExists('admins');
    }
};
