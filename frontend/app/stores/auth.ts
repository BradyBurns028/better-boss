import {authenticationApi, type RoleName} from '~/api/AuthenticationAPI'
import type { User, LoginPayload } from '~/api/AuthenticationAPI'
// @ts-ignore
import { defineStore, type Store } from 'pinia'

type AuthState = {
    token: string | null
    user: User | null
    loading: boolean
}

type AuthGetters = {
    isAuthenticated(state: AuthState): boolean
    fullName(state: AuthState): string
    role(state: AuthState): RoleName | null
    hasRole(state: AuthState): (role: string | string[]) => boolean
}

type AuthActions = {
    login(payload: LoginPayload): Promise<boolean>
    logout(): Promise<void>
    hydrate(): Promise<void>
}

type AuthStore = Store<'auth', AuthState, AuthGetters, AuthActions>

const TOKEN_KEY = 'authToken'
const USER_KEY  = 'authUser'

const getStoredToken = (): string | null =>
    typeof window !== 'undefined' ? localStorage.getItem(TOKEN_KEY) : null

const getStoredUser = (): User | null => {
    if (typeof window === 'undefined') return null
    try {
        const raw = localStorage.getItem(USER_KEY)
        return raw ? JSON.parse(raw) : null
    } catch { return null }
}

const setStoredAuth = (token: string | null, user: User | null) => {
    if (typeof window === 'undefined') return
    if (token) {
        localStorage.setItem(TOKEN_KEY, token)
    } else {
        localStorage.removeItem(TOKEN_KEY)
    }
    if (user) {
        localStorage.setItem(USER_KEY, JSON.stringify(user))
    } else {
        localStorage.removeItem(USER_KEY)
    }
}

export const useAuthStore = defineStore<'auth', AuthState, AuthGetters, AuthActions>('auth', {
    state: (): AuthState => ({
        token: getStoredToken(),
        user:  getStoredUser(),
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
        async login(this: AuthStore, payload: LoginPayload): Promise<Array> {
            this.loading = true
            try {
                const data = await authenticationApi.login(payload)
                if (!data) return false
                this.token = data.token
                this.user = data.user
                setStoredAuth(data.token, this.user)
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
                setStoredAuth(null, null)
                this.loading = false
            }
        },

        async hydrate(this: AuthStore): Promise<void> {
            const token = getStoredToken()
            if (!token) {
                this.token = null
                this.user  = null
                setStoredAuth(null, null)
                return
            }

            this.loading = true
            try {
                this.token = token
                const me: User | null = await authenticationApi.me()
                if (me) {
                    this.user = me
                    setStoredAuth(this.token, this.user)
                } else {
                    this.token = null
                    this.user  = null
                    setStoredAuth(null, null)
                }
            } finally {
                this.loading = false
            }
        },
    },
})