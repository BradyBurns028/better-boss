import {BaseApi} from "~/api/BaseAPI";
import {apiService} from '~/services/ApiService'

export type RoleName = 'admin' | 'owner' | 'faculty' | 'student' | string

export interface User {
    id: number
    first_name: string
    last_name: string
    email: string
    user_type: RoleName
    email_verified_at?: string | null
    created_at?: string
    updated_at?: string
}

export interface LoginPayload {
    email: string
    password: string
}

export interface LoginResponse {
    token: string
    user: User
}

/**
 * Auth API: login/logout/me
 * Uses your ApiResponse envelope and BaseApi.unwrap + toast notifications.
 */
export class AuthenticationApi extends BaseApi<never, {}> {
    constructor() {
        super('auth')
    }

    async login(body: LoginPayload) {
        // Expected: { success, data: { token, user }, ... }
        return await this.unwrap<LoginResponse>(
            apiService.post<LoginResponse>(`${this.resource}/login`, body),
            {success: 'Logged in', error: 'Invalid email or password'}
        )
    }

    async logout() {
        // Expected: { success, data: null, ... }
        return this.unwrap<null>(
            apiService.post<null>(`${this.resource}/logout`, {}),
            { success: 'Logged out', error: 'Logout failed' }
        )
    }
}

export const authenticationApi = new AuthenticationApi()
