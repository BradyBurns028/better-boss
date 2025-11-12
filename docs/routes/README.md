# API Routes and Filters

This document reflects the current API surface of the Laravel backend and documents supported query filters. All routes are served under `/api`. Unless marked public, endpoints require `auth:sanctum`.

—

## Authentication

- POST `/api/register` — Create a new user account.
- POST `/api/login` — Obtain auth token (Sanctum).
- GET `/api/me` — Return current user. (auth)
- POST `/api/logout` — Revoke token. (auth)

—

## Resources (REST)

Each resource below exposes standard RESTful routes via `Route::apiResource`:

- `GET /{resource}` (index)
- `POST /{resource}` (store)
- `GET /{resource}/{id}` (show)
- `PUT/PATCH /{resource}/{id}` (update)
- `DELETE /{resource}/{id}` (destroy)

All list endpoints support pagination via `?per_page=15` (1–100). Some endpoints support `include` to eager‑load relations, noted per resource.

### Users — `/api/users`

- Includes: loaded by default `admins`, `students.degreeProgram.department.organization`, `faculties.department.organization`, `students.faculty.user`.
- Filters: see Filters section: User.


### Students — `/api/students`

- Includes: `include=user,faculty,degreeProgram,degreeProgram.department`.
- Filters: see Filters section: Student.


### Faculties — `/api/faculties`

- Includes: `include=user,department,degreePrograms,advisees`.
- Extra: POST `/api/enroll-current-term` — Enroll all advisees into planned sections for the current (or provided) term/year.
- Filters: see Filters section: Faculty.


### Organizations — `/api/organizations`

- Includes: `include=admin,user,departments`.
- Filters: see Filters section: Organization.


### Departments — `/api/departments`

- Includes: `include=organization,degreePrograms,departmentChair,faculty`.
- Filters: see Filters section: Department.


### Degree Programs — `/api/degree_programs`

- Includes: `department,programChair`.
- Filters: see Filters section: Degree Program.


### Courses — `/api/courses`

- Includes: always loads `department, prerequisite, dependents, sections` (no `include` param).
- Filters: powerful course + section filters. See Filters: Course.


### Course Sections — `/api/course_sections`

- Includes: `include=course,instructor,plans`.
- Filters: see Filters section: Course Section.


### Plans of Study — `/api/plans_of_study`

- Includes: always loads `student, courses, sections, courses.sections`.
- Filters: see Filters section: Plan of Study.


### Planned Course Pivots — `/api/planned_course_pivots`

- Behavior: `POST` acts as upsert/delete based on payload. If an existing row is posted with both `term` and `year` omitted, it deletes that planned row; otherwise it creates/updates.
- Filters: see Filters section: Planned Course Pivot.


—

## Additional Endpoints

- GET `/api/organization-students` — All students within the authenticated faculty member’s organization. Supports `include` and Student filters. (auth)

—

## Filters

All filters use the pattern `?field[op]=value`. Unless noted, `op` may be one of: `eq`, `ne`, `lt`, `lte`, `gt`, `gte`, and some endpoints support `like` (case‑insensitive, `ilike`). Multiple filters can be combined.

Examples:

- `GET /api/users?first_name[like]=jo&user_type[eq]=student`
- `GET /api/courses?credits[gte]=3&name[like]=data`
- `GET /api/courses?matches[like]=CSC-130` (special normalized search)


### User Filters

- `first_name[like]` — case‑insensitive match
- `last_name[like]`
- `email[like]`
- `user_type[eq]`


### Student Filters

- `degree_program[eq]`
- `user_id[eq]`
- `faculty_id[eq]`


### Faculty Filters

- `department_id[eq]`
- `user_id[eq]`
- `role_type[eq]`
- `office[like]`


### Organization Filters

- `admin_id[eq]`
- `owner_id[eq]` — pass empty value to match NULL: `owner_id=`
- `name[like]`


### Department Filters

- `organization_id[eq]`
- `department_chair[eq]`
- `name[like]`


### Degree Program Filters

- `department_id[eq]`
- `program_chair[eq]`
- `name[like]`


### Course Section Filters

- `course_id[eq]`
- `section_number[eq]`
- `term[eq]`
- `year[eq|lt|lte|gt|gte]`
- `instructor_id[eq]`


### Plan of Study Filters

- `degree_program_id[eq]`
- `student_id[eq]`


### Planned Course Pivot Filters

- `plan_of_study_id[eq]`
- `course_id[eq]`
- `course_section_id[eq]` — pass empty to match NULL: `course_section_id=`
- `year[eq|lt|lte|gt|gte]`
- `term[eq]`
- `status[eq]` — one of `planned|active|completed|dropped`


### Course Filters (Courses + Sections)

- Course fields:
  - `department_id[eq]`
  - `prerequisite_id[eq]` — pass empty to match NULL: `prerequisite_id=`
  - `credits[eq|lt|lte|gt|gte]`
  - `name[like]`
  - `course_code[like]`
- Special search:
  - `matches[like]=CSC-130` — normalizes the code (e.g., `CSC130`) and matches `course_code`, `name`, or `description` case‑insensitively.
- Section‑level constraints (apply to related sections only and return courses having matching sections):
  - `term[eq]`
  - `year[eq|lt|lte|gt|gte]`
  - `instructor_id[eq]`


—

## Pagination and Includes

- Pagination: `?per_page=15` (min 1, max 100). Response includes `meta` with `page`, `total`, `per_page`, `last_page`, `current_page`.
- Includes: When supported, pass `?include=relation1,relation2`. Relations not listed under a resource’s Includes are ignored.

—

## Permissions

Most routes enforce fine‑grained permissions (see `PermissionEnum`). If a user lacks access, API returns a 403 with an error payload from `AbstractController::error()`.

