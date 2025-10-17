import {BaseApi} from "~/api/BaseAPI";

export enum UserTypeEnum {
    ADMIN = 'admin',
    STUDENT = 'student',
    FACULTY = 'faculty'
}

export type User = {
    id: number
    first_name: string
    last_name: string
    email: string
    user_type: UserTypeEnum
}

export class UserApi extends BaseApi<User, {}> {
    constructor() {
        super('users')
    }
}

export const userApi = new UserApi()
