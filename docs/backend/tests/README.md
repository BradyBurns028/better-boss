# Tests Overview

This document explains what tests exist (or will exist), how they’re organized, how to run them, and—most importantly—**what behaviors and edge cases they cover**. The suite uses **Pest** on top of **Laravel**.

---

## Goals

* Cover the **behaviors unique to our domain**, not Laravel internals.
* Keep **unit tests** small and focused on pure logic (scopes, custom casts) without a DB.
* Use **feature/integration tests** to prove relationships, events, and pivots work end‑to‑end with the real database and third‑party packages (Spatie Roles, Sanctum).

---

## How tests are organized

```
tests/
  Unit/
    Auth/
    Organizations/
    Courses/
  Feature/
    Auth/
    Organizations/
    Courses/
```

* **Unit/** = no DB; assert query shapes, enum casts, small helpers.
* **Feature/** = DB-backed; assert full behaviors, events, pivots, FKs.

> We intentionally **do not** unit-test exact `fillable`/`hidden` arrays or relation *types*—those are Laravel config and high-churn. We only add a targeted security test if a field must never be mass-assigned.

---

## Coverage by feature set

### 1) Auth

**Feature tests (DB-backed)**

* **Role auto-assignment on create**

    * When a `User` is created with a `user_type`, the `booted()` model event assigns the matching Spatie role.
    * No duplicate role assignment if a role already exists.
* **One-to-one links** from User to Admin/Organization/Student/Faculty (via `user_id` or `owner_id`).
* **Hidden attributes respected** (`password`, `remember_token` are not serialized in `toArray()`).

**Unit tests (no DB)**

* (Optional) **Enum casts**: `user_type` → `UserType` (kept minimal; behavioral parts covered in Feature).

**Edge cases covered**

* User created **without** `user_type` should **not** assign any role.
* User created **with** `user_type` when a role mapping already exists → no double assignment.
* Hidden fields remain hidden in arrays/JSON.

---

### 2) Organizations

**Feature tests (DB-backed)**

* **Organization** relationships

    * Belongs to `Admin` and owner `User` (`owner_id`).
    * Has many `Department`s; counts match; eager loading works.
* **Department** relationships

    * Belongs to `Organization`.
    * `departmentChair` points to a `Faculty`; reassigning updates relation.
    * Has many `DegreeProgram`s / `Faculty` / `Course`s.
* **Admin**

    * Belongs to `User`; has many `Organization`s.
* **Faculty**

    * Belongs to `User` and `Department`.
    * `departments()` returns chaired department when `department_chair` is set.
    * `degreePrograms()` returns programs where `program_chair` equals this faculty.
    * `advisees()` returns `Student`s with this `faculty_id`.
* **Soft deletes exist** on `organizations` and `departments`.

**Unit tests (no DB)**

* (Optional) `Faculty.role_type` enum cast → `FacultyRoleTypeEnum`.

**Edge cases covered**

* Assigning/removing a department chair updates the inverse relation.
* Creating a `DegreeProgram` under a `Department` ties back to the correct `Organization` via the department linkage.
* Deleting or soft-deleting an Organization behaves per current FK constraints (documented by the test—either restricted, cascade, or manual cleanup).

---

### 3) Courses

**Feature tests (DB-backed)**

* **Course ↔ Department**: `Course` belongs to `Department`.
* **Prerequisite graph**

    * Setting `prerequisite_id` links correctly; `dependents()` lists all children.
    * (If enforced) prevent self-prerequisite or cycles; otherwise, mark as expected behavior.
* **Course sections**

    * `sections()` create/read.
    * `instructor()` returns `Faculty` when `instructor_id` is set.
* **Enrollments (Student ↔ CourseSection)**

    * `Student.enrollments()` attaches/detaches; timestamps present.
    * `CourseSection.students()` reflects the same; duplicate attach prevented if unique key exists.
* **Degree requirements**

    * `Course.degreeRequirements()` attach persists `course_set` & `minimum_grade` and can be read back.
* **Plan of study + planned_courses**

    * `PlanOfStudy` belongs to `DegreeProgram` and `Student`.
    * `plan->courses()` pivot round-trips `year`, `term`, `status`, `course_section_id`.
    * `plan->sections()` returns **only** rows with non-null `course_section_id`.
* **PlannedCoursePivot scopes (with DB)**: each scope returns the expected count after seeding rows for all statuses.
* **Student derived organization**: `student->organization` resolves via `degreeProgram → department → organization` and returns `null` if any link is missing.
* **Finder/Scope behavior**: `Course::code('CS101')` finds the correct row.

**Unit tests (no DB)**

* **Scope query-shape**: `Course::scopeCode` adds a `where course_code = ?`.
* **Pivot scopes query-shape**: `planned/active/completed/dropped` add the correct `where status = ...`.
* (Optional) `PlannedCoursePivot` casts (`year` int, `status` enum).

**Edge cases covered**

* `sections()` → `plans()` only sees planned rows with **non-null** `course_section_id` (mirror of `PlanOfStudy.sections()` behavior).
* Plan-of-study pivot survives multiple attaches for the same course with different terms/years.
* Enrollment duplicate protection (if schema has composite unique); otherwise test shows current behavior and we can add a constraint later.
* Prerequisite cycles: if we don’t enforce prevention yet, the test clearly documents that and can be flipped when logic lands.

---

## Security-focused tests (targeted, not exhaustive)

We avoid asserting entire `fillable` arrays. Instead we add small, **behavioral** checks only where sensitive attributes could be mass-assigned or leaked:

* A single smoke test that `User::toArray()` does **not** include `password` / `remember_token`.
* (If needed) A test proving a specific forbidden attribute cannot be set via mass assignment.

---

## Conventions & tips

* Prefer **feature tests** whenever behavior crosses a model boundary or depends on DB state, events, or external packages.
* Keep **unit tests** tiny—assert one concern per `it()`; don’t hit the DB.
* Name tests by behavior, not method, e.g., `it('auto-assigns a role on create when user_type is set')`.
* Use factories with clear states (e.g., `withChair`, `withInstructor`) to keep test setup readable.
* For Spatie roles: seed once per file (Pest `beforeEach`/`beforeAll`) and `forgetCachedPermissions()`.
* Document current behavior even if imperfect (e.g., no cycle prevention). Tests serve as executable docs until we improve the logic.

---

## Quick checklist for adding a new test

* Is this pure logic? → **Unit**.
* Does it require DB/relationships/events/pivots/external packages? → **Feature**.
* Is it testing Laravel config (fillable/hidden/types)? → Usually **skip** unless it protects a security boundary.
* Name the spec by behavior; keep the setup minimal with factories/states; assert final, observable outcomes.
