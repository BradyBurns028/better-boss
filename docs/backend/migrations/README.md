# Database Schema & Migrations Documentation

This document describes the database structure, relationships, and migrations for the Laravel backend.

---

## Overview

The database models a multi-user environment with various user types (`Admin`, `Faculty`, `Student`, etc.) and organizational hierarchies.  

> **Note:** All migrations can be found in the `backend/database/migrations` directory.

---

## Tables and Relationships

### **1. users**
The base table for all authenticated users.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| first_name | string | User's first name |
| last_name | string | User's last name |
| email | string (unique) | User’s email |
| password | string | Encrypted password |
|user_type | enum('admin', 'faculty', 'student') | Type of user |
| email_verified_at | timestamp (nullable) | Email verification date |
| deleted_at | timestamp (nullable) | For soft deletes |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- One to one relationship with either `Admin`, `Faculty`, or `Student` profiles
- May optionally own an `Organization`

---

### **2. admins**
Represents site admin and developers who can manage organizations.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| user_id | bigint (FK → users.id) | References associated user |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- Belongs to one `User` 
- May have many `Organizations`

---

### **3. organizations**
Represents organizations or universities managed by admins.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| name | string | Organization name |
| admin_id | bigint (FK → admins.id) | Linked admin |
| owner_id | bigint (nullable, FK → users.id) | Optional owner user |
| address | string | Physical address |
| deleted_at | timestamp (nullable) | Soft delete support |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- Belongs to one `Admin`
- Optionally belongs to one `User` (owner)
- Has many `Departments`

---

### **4. faculties**
Represents faculty members (professors, instructors, school administrators, etc.).

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| user_id | bigint (FK → users.id) | Associated user record |
| department_id | bigint (FK → departments.id) | Linked department |
| office | string (nullable) | Office location |
| role_type | string | Faculty role (e.g., “Professor”) |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- Belongs to a `User`
- Belongs to a `Department`
- May have (advise) many `Students` (but not required)

> **Note:** `role_type` is currently a string but should be treated as an `FacultyRoleTypeEnum`

> **Note:** Every `Student` belongs to a `Faculty`, but not every `Faculty` has a `Student`.

---

### **5. departments**
Represents departments within an organization.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| name | string | Department name |
| organization_id | bigint (FK → organizations.id) | Linked organization |
| department_chair | bigint (FK → faculties.id) | Faculty chairperson |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- Belongs to an `Organization`
- Has many `Faculties`
- Has one `Faculty` as the department chair
- May have many `DegreePrograms`

> **Note:** Every `degree_program` belongs to a `department`, but not every `department` has a `degree_program`.
---

### **6. degree_programs**
Represents degree programs offered under a department.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| name | string | Program name |
| department_id | bigint (FK → departments.id) | Linked department |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- Belongs to a `Department`
- Has many `Students`

---

### **7. students**
Represents enrolled students.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint (PK) | Unique identifier |
| user_id | bigint (FK → users.id) | Linked user account |
| faculty_id | bigint (FK → faculties.id) | Assigned faculty member |
| degree_program_id | bigint (FK → degree_programs.id) | Enrolled degree program |
| created_at / updated_at | timestamps | Auto-managed by Laravel |

**Relationships**
- Belongs to a `User`
- Belongs to a `Faculty` (advisor)
- Belongs to a `DegreeProgram` (through department structure)

---

### **8. courses**
Represents courses offered by departments.

| Column | Type | Description                           |
|--------|------|---------------------------------------|
| id | bigint (PK) | Unique identifier                     |
| created_at / updated_at | timestamps | Auto-managed by Laravel               |
| course_code | string | e.g., "CSC 130"                       |
| name | string | Course title                          |
| description | text (nullable) | Course description                    |
| credits | integer | Credit hours                          |
| department_id | bigint (FK → departments.id) | Owning department                     |
| prerequisite_id | bigint (nullable, FK → courses.id) | Optional self-referential prerequisite |

**Relationships**
- Belongs to a `Department`
- Optional self-referential prerequisite (points to another Course)
- Has many `CourseSections`

---

### **9. course_sections**
Represents individual course offerings (sections) in a term.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Unique identifier |
| created_at / updated_at | timestamps | Auto-managed by Laravel |
| course_id | bigint (FK → courses.id) | Parent course |
| section_number | integer | Section index (e.g., 1, 2) |
| term | string | Term label (e.g., "Fall") |
| year | integer | Year (e.g., 2025) |
| time | time | Meeting time |
| instructor_id | bigint (FK → faculties.id) | Faculty teaching the section |
| capacity | integer | Enrollment capacity |
| room_number | string | Room identifier |

**Relationships**
- Belongs to a `Course` and a `Faculty` (instructor)

---

### **10. plan_of_studies**
Represents a student's plan of study for a degree program.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Unique identifier |
| created_at / updated_at | timestamps | Auto-managed by Laravel |
| degree_program_id | bigint (FK → degree_programs.id) | Degree program for the plan |
| student_id | bigint (FK → students.id) | Owner student |

**Relationships**
- Belongs to a `DegreeProgram` and a `Student`
- Has many `PlannedCourses`

---

### **11. degree_requirements**
Maps required courses for a degree program (composite primary key).

Pivot table for many-to-many relationship between `DegreePrograms` and `Courses`.

| Column | Type | Description |
|--------|------|-------------|
| degree_program_id | bigint (FK → degree_programs.id) | Part of composite PK |
| course_id | bigint (FK → courses.id) | Part of composite PK |
| course_set | integer | Group id for interchangeable courses |
| minimum_grade | integer | Minimum required grade (numeric threshold) |

**Primary Key**
- Composite primary key on (degree_program_id, course_id)

**Relationships**
- Maps required `Courses` to `DegreePrograms`
- `course_set` groups courses that can substitute for one another

---

### **12. planned_courses**
Tracks courses planned or taken within a `PlanOfStudy` (composite primary key).

pivot table for many-to-many relationship between `PlanOfStudies` and `Courses`.

| Column | Type | Description |
|--------|------|-------------|
| plan_of_study_id | bigint (FK → plan_of_studies.id) | Part of composite PK |
| course_id | bigint (FK → courses.id) | Part of composite PK |
| year | integer | Planned/recorded year |
| term | string | Planned/recorded term |
| status | string | e.g., "planned", "active", "completed", "dropped" |
| course_section_id | bigint (nullable, FK → course_sections.id) | Optional section assignment |

**Primary Key**
- Composite primary key on (plan_of_study_id, course_id)

**Relationships**
- Belongs to `PlanOfStudy` and `Course`
- Optionally links to a `CourseSection` (for assigned/active courses)

**Notes**
- Composite primary keys are used for mapping tables (`degree_requirements`, `planned_courses`).
- Status is stored as a string; consider an enum or lookup table for stricter validation.

---

## Entity Relationship Summary

This section summarizes all relationships between entities in plain language.

- **User → Admin:** One-to-One
- **User → Faculty:** One-to-One
- **User → Student:** One-to-One
- **User → Organization (Owner):** One-to-One (optional)
- **Admin → Organizations:** One-to-Many
- **Organization → Departments:** One-to-Many
- **Department → Faculties:** One-to-Many
- **Department → DegreePrograms:** One-to-Many (optional)
- **Department → Faculty (Chair):** One-to-One
- **Faculty → Students:** One-to-Many (optional)
- **DegreeProgram → Student:** One-to-Many (through department)
