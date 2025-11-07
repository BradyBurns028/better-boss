<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case ADMINISTRATOR = 'administrator';
    case STAFF = 'staff';
    case INSTRUCTOR = 'instructor';
    case STUDENT = 'student';
}