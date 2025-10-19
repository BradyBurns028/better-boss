declare module 'primevue/toastservice' {
    const ToastService: any
    export default ToastService
}

declare module '@primeuix/themes/aura' {
    const Aura: any
    export default Aura
}

declare module 'nuxt/config' {
    import type { NuxtConfig } from 'nuxt/schema'
    export function defineNuxtConfig<T extends NuxtConfig>(config: T): T
    export * from 'nuxt/schema'
}
