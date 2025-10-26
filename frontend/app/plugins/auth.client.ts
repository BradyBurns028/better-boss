import { useAuthStore } from '~/stores/auth'

export default defineNuxtPlugin(async (): Promise<void> => {
    const auth = useAuthStore()
    await auth.hydrate()
})
