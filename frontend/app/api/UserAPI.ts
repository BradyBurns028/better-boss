import {BaseApi} from "~/api/BaseAPI";
import type {Student} from "~/api/StudentAPI";

export enum UserTypeEnum {
    ADMIN = 'admin',
    STUDENT = 'student',
    FACULTY = 'faculty'
}

export interface User {
    id: number
    first_name: string
    last_name: string
    email: string
    user_type: UserTypeEnum
    student: Student
    email_verified_at?: string | null
    created_at?: string
    updated_at?: string
}

export class UserApi extends BaseApi<User, never> {
    constructor() {
        super('users')
    }
}

export const userApi = new UserApi()
