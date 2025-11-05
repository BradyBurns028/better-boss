# Roles and Permissions (Spatie Laravel Permission)

This migration defines the complete list of roles and permissions used in the application.  
Each role is assigned a specific set of permissions that determine what actions a user can perform within the system.

---

## Permissions Overview

### User Management
- `view_users`
- `create_users`
- `edit_users`
- `delete_users`
- `assign_roles`
- `index_users`
- `index_user_details`

### Student Management
- `view_students`
- `view_student_details`
- `create_students`
- `edit_students`
- `delete_students`
- `view_advisees`

### Faculty Management
- `view_faculty`
- `view_faculty_details`
- `create_faculty`
- `edit_faculty`
- `delete_faculty`
- `view_administrators`
- `view_instructors`
- `view_staff`

### Departments
- `view_departments`
- `create_departments`
- `edit_departments`
- `delete_departments`

### Organizations
- `view_organizations`
- `create_organizations`
- `edit_organizations`
- `delete_organizations`
- `index_organizations`
- `index_organization_details`

### Degree Programs
- `view_degree_programs`
- `create_degree_programs`
- `edit_degree_programs`
- `delete_degree_programs`

### Degree Requirements
- `view_degree_requirements`
- `edit_degree_requirements`

### Courses
- `view_courses`
- `create_courses`
- `edit_courses`
- `delete_courses`

### Course Sections
- `view_course_sections`
- `create_course_sections`
- `edit_course_sections`
- `delete_course_sections`
- `view_enrolled_students`

### Plans of Study
- `view_plans_of_study`
- `create_plans_of_study`
- `edit_plans_of_study`
- `delete_plans_of_study`
- `index_plans_of_study`

---

## Roles and Assigned Permissions

### **Admin**
- Has **all permissions** listed above.
- Full control over all system resources and user management.

---

### **Administrator**
Has broad management capabilities including students, faculty, departments, organizations, degree programs, and courses.

**Permissions include:**
- All *Student Management* permissions
- All *Faculty Management* permissions
- All *Department* permissions
- Organization index access (`index_organizations`, `index_organization_details`)
- All *Degree Program* and *Degree Requirement* permissions
- All *Course* and *Course Section* permissions

---

### **Staff**
Primarily focused on internal visibility and organizational overview.

**Permissions include:**
- `view_faculty`
- `view_departments`
- `index_organizations`

---

### **Instructor**
Can view and manage their assigned students and educational content.

**Permissions include:**
- `view_advisees`
- `view_faculty`
- `view_departments`
- `index_organizations`
- `view_degree_programs`
- `view_degree_requirements`
- `view_courses`
- `view_course_sections`
- `view_enrolled_students`
- `view_plans_of_study`

---

### **Student**
Can view information related to their program and manage their plan of study.

**Permissions include:**
- `view_instructors`
- `view_departments`
- `index_organizations`
- `view_degree_programs`
- `view_degree_requirements`
- `view_courses`
- `view_course_sections`
- `index_plans_of_study`
- `create_plans_of_study`
- `edit_plans_of_study`
- `delete_plans_of_study`

---
