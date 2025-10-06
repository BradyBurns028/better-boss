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