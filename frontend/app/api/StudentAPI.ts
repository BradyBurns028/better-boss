import {BaseApi} from "~/api/BaseAPI";
import type {Organization} from "~/api/OrganizationAPI";
import type {User} from "~/api/UserAPI";
import type {Course, CourseSection} from "~/api/CourseAPI";

export interface Student {
    id: number
    organization: Organization
    advisor: Object
    degree_program: Object
    user: User
    enrollments?: Enrollment
}

export interface Enrollment {
    grade: string
    course: Course
    course_section: CourseSection
}

export class StudentApi extends BaseApi<Student, never> {
    constructor() {
        super('students')
    }
}

export const studentApi = new StudentApi()
