<?php

namespace app\Enums;

enum RoleEnum: string
{
    case SITE_ADMIN = 'site_admin';
    case ADMINISTRATOR = 'administrator';
    case STAFF = 'staff';
    case INSTRUCTOR = 'instructor';
    case STUDENT = 'student';
}