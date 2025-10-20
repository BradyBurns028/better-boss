# API Routes Documentation

This document lists all available API routes, organized by controller.  
All controllers extend `AbstractController`, which provides a unified `response()` method for consistent API output.

---

## AdminController

**Base Route:** `/api/admins`

### POST `/api/admins`
Create a new admin account and linked user.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| first_name | string | yes | Max 255 chars |
| last_name | string | yes | Max 255 chars |
| email | email | yes | Must be unique in `users` |
| password | string | yes | Min 8 chars, must be confirmed |

**Relationships Loaded**
- user

### GET `/api/admins/{id}`
Retrieve a specific admin and related user data.

### PUT/PATCH `/api/admins/{id}`
Update the linked user’s information.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| first_name | string | optional |  |
| last_name | string | optional |  |
| email | email | optional | Must be unique |
| password | string | optional | Confirmed, min 8 chars |

### DELETE `/api/admins/{id}`
Not implemented.

---

## UserController

**Base Route:** `/api/users`

### POST `/api/users`
Create a general user record.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| first_name | string | yes |  |
| last_name | string | yes |  |
| email | email | yes | Unique |
| password | string | yes | Min 8 chars, confirmed |
| user_type | string | optional | Optional user classification |

**Relationships Loaded**
- admins  
- organizations  
- students  
- faculties

### GET `/api/users/{id}`
Retrieve a user with all related entities.

### PUT/PATCH `/api/users/{id}`
Update user information.

### DELETE `/api/users/{id}`
Not implemented.

---

## StudentController

**Base Route:** `/api/students`

### POST `/api/students`
Create a new student and linked user.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| first_name | string | yes |  |
| last_name | string | yes |  |
| email | email | yes | Unique |
| password | string | yes | Min 8 chars, confirmed |
| degree_program | integer | yes | Must exist in `degree_programs` |
| faculty_id | integer | optional | Must exist in `faculties` |

**Relationships Loaded**
- user  
- degreeProgram  
- degreeProgram.department.organization  
- faculty

### GET `/api/students/{id}`
Return a student with all related data.

### PUT/PATCH `/api/students/{id}`
Update student and linked user information.

### DELETE `/api/students/{id}`
Not implemented.

---

## FacultyController

**Base Route:** `/api/faculties`

### POST `/api/faculties`
Create a faculty member linked to a user.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| first_name | string | yes |  |
| last_name | string | yes |  |
| email | email | yes | Unique |
| password | string | yes | Min 8 chars, confirmed |
| office | string | optional |  |
| role_type | string | yes | Defines role type |
| department_id | integer | yes | Must exist in `departments` |

**Relationships Loaded**
- user  
- department  
- degreePrograms  
- advisees

### GET `/api/faculties/{id}`
Retrieve faculty details with related data.

### PUT/PATCH `/api/faculties/{id}`
Update faculty and linked user information.

### DELETE `/api/faculties/{id}`
Not implemented.

---

## OrganizationController

**Base Route:** `/api/organizations`

### POST `/api/organizations`
Create a new organization.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| name | string | yes |  |
| admin_id | integer | yes | Must exist in `admins` |
| owner_id | integer | optional | Must exist in `users` |
| address | string | optional | Max 1000 chars |

**Relationships Loaded**
- admin  
- user  
- departments

### GET `/api/organizations/{id}`
Return an organization with its admin, user, and departments.

### PUT/PATCH `/api/organizations/{id}`
Update organization details.

### DELETE `/api/organizations/{id}`
Not implemented.

---

## DepartmentController

**Base Route:** `/api/departments`

### POST `/api/departments`
Create a new department under an organization.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| name | string | yes |  |
| organization_id | integer | yes | Must exist in `organizations` |
| department_chair | integer | optional | Must exist in `faculties` |

**Relationships Loaded**
- organization  
- degreePrograms  
- faculty  
- departmentChair

### GET `/api/departments/{id}`
Retrieve a department with all related entities.

### PUT/PATCH `/api/departments/{id}`
Update department information.

### DELETE `/api/departments/{id}`
Not implemented.

---

## DegreeProgramController

**Base Route:** `/api/degree-programs`

### POST `/api/degree-programs`
Create a degree program under a department.

**Validations**
| Field | Type | Required | Notes |
|--------|------|-----------|-------|
| name | string | yes |  |
| department_id | integer | yes | Must exist in `departments` |
| program_chair | integer | optional | Must exist in `faculties` |

**Relationships Loaded**
- department  
- programChair  
- students

### GET `/api/degree-programs/{id}`
Retrieve a degree program with related data.

### PUT/PATCH `/api/degree-programs/{id}`
Update degree program details.

### DELETE `/api/degree-programs/{id}`
Not implemented.

---

## Summary of Implemented Routes

| Controller | Method | Route | Status |
|-------------|---------|--------|---------|
| AdminController | POST | /api/admins | Working |
| AdminController | GET | /api/admins/{id} | Working |
| AdminController | PUT/PATCH | /api/admins/{id} | Working |
| UserController | POST | /api/users | Working |
| UserController | GET | /api/users/{id} | Working |
| UserController | PUT/PATCH | /api/users/{id} | Working |
| StudentController | POST | /api/students | Working |
| StudentController | GET | /api/students/{id} | Working |
| StudentController | PUT/PATCH | /api/students/{id} | Working |
| FacultyController | POST | /api/faculties | Working |
| FacultyController | GET | /api/faculties/{id} | Working |
| FacultyController | PUT/PATCH | /api/faculties/{id} | Working |
| OrganizationController | POST | /api/organizations | Working |
| OrganizationController | GET | /api/organizations/{id} | Working |
| OrganizationController | PUT/PATCH | /api/organizations/{id} | Working |
| DepartmentController | POST | /api/departments | Working |
| DepartmentController | GET | /api/departments/{id} | Working |
| DepartmentController | PUT/PATCH | /api/departments/{id} | Working |
| DegreeProgramController | POST | /api/degree-programs | Working |
| DegreeProgramController | GET | /api/degree-programs/{id} | Working |
| DegreeProgramController | PUT/PATCH | /api/degree-programs/{id} | Working |
