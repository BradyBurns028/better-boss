import {BaseApi} from "~/api/BaseAPI";
import type {Organization} from "~/api/OrganizationAPI";
import type {User} from "~/api/UserAPI";

export interface Student {
    id: number
    organization: Organization
    advisor: Object
    degree_program: Object
    user: User
}

export class FacultyApi extends BaseApi<Student, never> {
    constructor() {
        super('faculties')
    }
}

export const facultyApi = new FacultyApi()
