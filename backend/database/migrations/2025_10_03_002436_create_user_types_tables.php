<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('admin_id')->constrained('admin');
            $table->foreignId('owner_id')->constrained('users');
            $table->string('address');
            $table->timestamps();
        });

        Schema::create('faculty', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('office')->nullable();
            $table->string('role_type')->default('faculty');
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('degree_program', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('program_chair')->constrained('faculty');
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->ForeignId('degree_program')->constrained('degree_program');
            $table->ForeignId('user_id')->constrained('users')->onDelete('cascade');
            $table->ForeignId('faculty_id')->constrained('faculty');
        });

        Schema::table('faculty', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('department_chair')->constrained('faculty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('faculty');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('admin');
        Schema::dropIfExists('degree_program');
    }
};
