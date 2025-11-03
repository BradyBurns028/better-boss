import {BaseApi} from "~/api/BaseAPI";
import {apiService} from '~/services/ApiService'
import type {User} from "~/api/UserAPI";

export interface LoginPayload {
    email: string
    password: string
}

export interface LoginResponse {
    token: string
    user: User
}

/**
 * Auth API: login/logout
 * Uses your ApiResponse envelope and BaseApi.unwrap + toast notifications.
 */
export class AuthenticationApi extends BaseApi<never, {}> {
    constructor() {
        super('')
    }

    async login(body: LoginPayload): Promise<LoginResponse | null> {
        return await this.unwrap<LoginResponse>(
            apiService.post<LoginResponse>(`${this.resource}login`, body),
            {
                error: 'Unable to Login'
            }
        )
    }

    async logout(): Promise<string | null> {
        return this.unwrap<string>(
            apiService.post<string>(`${this.resource}logout`, {}),
        )
    }

    async me(): Promise<User | null> {
        return this.unwrap<User>(
            apiService.get<User>(`${this.resource}me`, {}),
        )
    }
}

export const authenticationApi = new AuthenticationApi()
