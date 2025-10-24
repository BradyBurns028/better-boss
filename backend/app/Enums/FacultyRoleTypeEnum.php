<?php

namespace App\Enums;

enum FacultyRoleTypeEnum: string {
    case ASSISTANT_PROFESSOR = 'assistant_professor';
    case ASSOCIATE_PROFESSOR = 'associate_professor';
    case INSTRUCTOR = 'instructor';
    case PROFESSOR = 'professor';
    case ADMINISTRATOR = 'administrator';
}