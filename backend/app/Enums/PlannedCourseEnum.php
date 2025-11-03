<?php

namespace App\Enums;

enum PlannedCourseEnum: string {
    case PLANNED = 'planned';
    case COMPLETED = 'completed';
    case ACTIVE = 'active';
    case DROPPED = 'dropped';
}