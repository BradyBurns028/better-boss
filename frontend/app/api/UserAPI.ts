import {BaseApi} from "~/api/BaseAPI";
import {apiService} from '~/services/ApiService'

export type User = {
    id: number
    name: string
}

export class UserApi extends BaseApi<User, {}> {
    constructor() {
        super('users')
    }
}

export const userApi = new UserApi()
