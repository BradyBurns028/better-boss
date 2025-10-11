import { authenticationApi } from '~/api/AuthenticationAPI'
import type { User, LoginPayload } from '~/api/AuthenticationAPI'

/**
 * Pinia Auth Store
 * - Persists token in localStorage
 * - Adds/removes Bearer header via ApiService.getHeaders (already reads from localStorage)
 * - Exposes handy getters and actions
 */

type AuthState = {
    token: string | null
    user: User | null
    loading: boolean
}

const TOKEN_KEY = 'authToken'

const getStoredToken = (): string | null =>
    typeof window !== 'undefined' ? localStorage.getItem(TOKEN_KEY) : null

const setStoredToken = (token: string | null) => {
    if (typeof window === 'undefined') return
    if (token) localStorage.setItem(TOKEN_KEY, token)
    else localStorage.removeItem(TOKEN_KEY)
}

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        token: getStoredToken(),
        user: null,
        loading: false,
    }),

    getters: {
        isAuthenticated: (s: { token: any; user: any }) => !!s.token && !!s.user,
        fullName: (s: { user: { first_name: any; last_name: any } }) => (s.user ? `${s.user.first_name} ${s.user.last_name}` : ''),
        role:    (s) => s.user?.user_type ?? null,
        hasRole: (s) => (role: string | string[]) => {
            if (!s.user?.user_type) return false
            if (Array.isArray(role)) return role.includes(s.user.user_type)
            return s.user.user_type === role
        },
    },

    actions: {
        /**
         * Attempt login, store token, set user.
         * Returns true on success, false on failure (401, etc).
         */
        async login(payload: LoginPayload): Promise<boolean> {
            this.loading = true
            try {
                const data = await authenticationApi.login(payload)
                if (!data) return false
                this.token = data.token
                this.user = data.user
                setStoredToken(data.token)
                return true
            } finally {
                this.loading = false
            }
        },

        /**
         * Logout regardless of server response (best-effort),
         * then clear local state/token.
         */
        async logout(): Promise<void> {
            this.loading = true
            try {
                // Best-effort; even if it 401s (expired token), we still clear locally.
                await authenticationApi.logout()
            } catch {
                // ignore
            } finally {
                this.token = null
                this.user = null
                setStoredToken(null)
                this.loading = false
            }
        },

        /**
         * Rehydrate on app load if a token exists in localStorage.
         * If server says 401/invalid, clear token.
         */
        async hydrate(): Promise<void> {
            // Only try if we have a stored token
            const token = getStoredToken()
            if (!token) {
                this.token = null
                this.user = null
                return
            }
            this.loading = true
            try {
                this.token = token
                const me = await authenticationApi.me()
                if (me) {
                    this.user = me
                } else {
                    // Failed (likely 401)
                    this.token = null
                    setStoredToken(null)
                    this.user = null
                }
            } finally {
                this.loading = false
            }
        },
    },
})
