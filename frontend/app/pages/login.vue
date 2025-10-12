<script lang="ts">
import { useAuthStore } from '~/stores/auth'
import type { LoginPayload } from '~/api/AuthenticationAPI'

export default defineNuxtComponent({
    name: 'Login',
    data() {
        return {
            authStore: useAuthStore(),
            email: '',
            password: '',
            loading: false
        }
    },
    methods: {
        async handleLogin(this: any) {
            const payload: LoginPayload = { email: this.email, password: this.password }
            const ok = await this.authStore.login(payload)
            if (ok) {
                navigateTo('dashboard')
            }
        },
    },

    async mounted() {
        if (this.authStore.isAuthenticated) {
            navigateTo('dashboard')
        }
    },
})
</script>
<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-blue-600 text-white text-center py-10 text-4xl font-bold">
            Better B.O.S.S.
        </header>

        <!-- Login form container -->
        <div class="min-h-[60vh] flex items-start justify-center pt-10 px-4">
            <div class="w-full max-w-md bg-white shadow rounded-lg p-8">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Sign in</h2>

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="email"
                            v-model="email"
                            type="email"
                            required
                            autocomplete="username"
                            class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-200"
                            placeholder="you@example.com"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input
                            id="password"
                            v-model="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-200"
                            placeholder="••••••••"
                            @keyup.enter="handleLogin"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2 px-4 rounded-md bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="loading"
                    >
                        <span v-if="!loading">Sign In</span>
                        <span v-else>Signing in…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
