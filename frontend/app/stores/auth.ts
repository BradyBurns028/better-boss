import {authenticationApi, type LoginResponse} from '~/api/AuthenticationAPI'
import type { LoginPayload } from '~/api/AuthenticationAPI'
// @ts-ignore
import { defineStore, type Store } from 'pinia'
import type {User, UserTypeEnum} from "~/api/UserAPI";

type AuthState = {
    token: string | null
    user: User | null
    loading: boolean
}

type AuthGetters = {
    isAuthenticated(state: AuthState): boolean
    fullName(state: AuthState): string
    role(state: AuthState): UserTypeEnum | null
    hasRole(state: AuthState): (role: string | string[]) => boolean
}

type AuthActions = {
    login(payload: LoginPayload): Promise<boolean>
    logout(): Promise<void>
    hydrate(): Promise<void>
}

type AuthStore = Store<'auth', AuthState, AuthGetters, AuthActions>

const TOKEN_KEY = 'authToken'

const getStoredToken = (): string | null =>
    typeof window !== 'undefined' ? localStorage.getItem(TOKEN_KEY) : null

const setStoredToken = (token: string | null, user: any) => {
    if (typeof window === 'undefined') return
    if (token) {
        localStorage.setItem('authToken', token)
        localStorage.setItem('authUser', user)
    }  else {
        localStorage.removeItem(TOKEN_KEY)
    }
}

export const useAuthStore = defineStore<'auth', AuthState, AuthGetters, AuthActions>('auth', {
    state: (): AuthState => ({
        token: getStoredToken(),
        user:
            typeof window !== 'undefined'
                ? JSON.parse(localStorage.getItem('authUser') || 'null')
                : null,
        loading: false,
    }),

    getters: {
        isAuthenticated: (s: { token: any; user: any }) => !!s.token && !!s.user,
        fullName: (s: { user: { first_name: any; last_name: any } }) => (s.user ? `${s.user.first_name} ${s.user.last_name}` : ''),
        role: (s: { user: { user_type: any } }) => s.user?.user_type ?? null,
        id: (s: { user: { id: any } }) => s.user?.id ?? null,
        hasRole: (s: { user: { user_type: any } }) => (role: string | string[]) => {
            const u = s.user?.user_type
            if (!u) return false
            return Array.isArray(role) ? role.includes(u) : u === role
        },
    },

    actions: {
        async login(this: AuthStore, payload: LoginPayload): Promise<boolean | User> {
            this.loading = true
            try {
                const data: LoginResponse | null = await authenticationApi.login(payload)
                if (!data) return false
                this.token = data.token
                this.user = data.user
                setStoredToken(data.token, this.user)
                return this.user
            } finally {
                this.loading = false
            }
        },

        async logout(this: AuthStore): Promise<void> {
            this.loading = true
            try {
                await authenticationApi.logout()
            } catch { /* noop */ }
            finally {
                this.token = null
                this.user  = null
                setStoredToken(null, null)
                this.loading = false
            }
        },

        async hydrate(this: AuthStore): Promise<void> {
            const token = getStoredToken()
            if (!token) {
                this.token = null
                this.user = null
                return
            }
            this.loading = true
            try {
                this.token = token
            } finally {
                this.loading = false
            }
        },
    },
})