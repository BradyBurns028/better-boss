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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('course_code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('credits');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('prerequisite_id')->nullable()->constrained('courses');
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('course_id')->constrained('courses');
            $table->integer('section_number');
            $table->string('term');
            $table->integer('year');
            $table->time('time');
            $table->foreignId('instructor_id')->constrained('faculties');
            $table->integer('capacity');
            $table->string('room_number');
        });

        Schema::create('plan_of_studies', function (Blueprint $table){
            $table->id();
            $table->timestamps();
            $table->foreignId('degree_program_id')->constrained('degree_programs');
            $table->foreignId('student_id')->constrained('students');
        });

        Schema::create('degree_requirements', function (Blueprint $table){
            $table->foreignId('degree_program_id')->constrained('degree_programs');
            $table->foreignId('course_id')->constrained('courses');
            $table->primary(['degree_program_id', 'course_id']);
            $table->integer('course_set'); //for courses that can subsitute each other
            $table->integer('minimum_grade');
        });

        Schema::create('planned_courses', function (Blueprint $table){
            $table->foreignId('plan_of_study_id')->constrained('plan_of_studies');
            $table->foreignId('course_id')->constrained('courses');
            $table->primary(['plan_of_study_id', 'course_id']);
            $table->integer('year');
            $table->string('term');
            $table->string('status'); //planned, completed, active, dropped
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('planned_courses');
        Schema::dropIfExists('degree_requirements');
        Schema::dropIfExists('plan_of_studies');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
    }
};
