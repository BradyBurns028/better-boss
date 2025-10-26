<?php

namespace app\Enums;

enum PermissionEnum: string
{
    // All users
    case VIEW_USERS = 'view_users';
    case CREATE_USERS = 'create_users';
    case EDIT_USERS = 'edit_users';
    case DELETE_USERS = 'delete_users';
    case ASSIGN_ROLES = 'assign_roles';
    case INDEX_USERS = 'index_users';
    case INDEX_USER_DETAILS = 'index_user_details';

    // Students
    case VIEW_STUDENTS = 'view_students';
    case VIEW_STUDENT_DETAILS = 'view_student_details';
    case CREATE_STUDENTS = 'create_students';
    case EDIT_STUDENTS = 'edit_students';
    case DELETE_STUDENTS = 'delete_students';
    case VIEW_ADVISEES = 'view_advisees';

    // Faculty
    case VIEW_FACULTY = 'view_faculty';
    case VIEW_FACULTY_DETAILS = 'view_faculty_details';
    case CREATE_FACULTY = 'create_faculty';
    case EDIT_FACULTY = 'edit_faculty';
    case VIEW_ADMINISTRATORS = 'view_administrators';
    case VIEW_INSTRUCTORS = 'view_instructors';
    case VIEW_STAFF = 'view_staff';

    // Departments
    case VIEW_DEPARTMENTS = 'view_departments';
    case CREATE_DEPARTMENTS = 'create_departments';
    case EDIT_DEPARTMENTS = 'edit_departments';
    case DELETE_DEPARTMENTS = 'delete_departments';

    // Organizations
    case VIEW_ORGANIZATIONS = 'view_organizations';
    case CREATE_ORGANIZATIONS = 'create_organizations';
    case EDIT_ORGANIZATIONS = 'edit_organizations';
    case DELETE_ORGANIZATIONS = 'delete_organizations';
    case INDEX_ORGANIZATIONS = 'index_organizations';
    case INDEX_ORGANIZATION_DETAILS = 'index_organization_details';

    // Degree Programs
    case VIEW_DEGREE_PROGRAMS = 'view_degree_programs';
    case CREATE_DEGREE_PROGRAMS = 'create_degree_programs';
    case EDIT_DEGREE_PROGRAMS = 'edit_degree_programs';
    case DELETE_DEGREE_PROGRAMS = 'delete_degree_programs';

    // Degree Requirements
    case VIEW_DEGREE_REQUIREMENTS = 'view_degree_requirements';
    case EDIT_DEGREE_REQUIREMENTS = 'edit_degree_requirements';

    // Courses
    case VIEW_COURSES = 'view_courses';
    case CREATE_COURSES = 'create_courses';
    case EDIT_COURSES = 'edit_courses';
    case DELETE_COURSES = 'delete_courses';

    // Course Sections
    case VIEW_COURSE_SECTIONS = 'view_course_sections';
    case CREATE_COURSE_SECTIONS = 'create_course_sections';
    case EDIT_COURSE_SECTIONS = 'edit_course_sections';
    case DELETE_COURSE_SECTIONS = 'delete_course_sections';
    case VIEW_ENROLLED_STUDENTS = 'view_enrolled_students';

    // Plans of Study
    case VIEW_PLANS_OF_STUDY = 'view_plans_of_study';
    case CREATE_PLANS_OF_STUDY = 'create_plans_of_study';
    case EDIT_PLANS_OF_STUDY = 'edit_plans_of_study';
    case DELETE_PLANS_OF_STUDY = 'delete_plans_of_study';
    case INDEX_PLANS_OF_STUDY = 'index_plans_of_study';
}