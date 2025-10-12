<script lang="ts">
import { useAuthStore } from '~/stores/auth'

export default defineNuxtComponent({
    name: 'Home',
    async mounted() {
        const auth = useAuthStore()

        if (!auth.user && auth.token) {
            try {
                await auth.hydrate()
            } catch { /* ignore */ }
        }

        navigateTo(auth.isAuthenticated ? '/dashboard' : '/login')
    },
})
</script>

<template>

</template>