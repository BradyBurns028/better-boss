import ToastService from 'primevue/toastservice'

export default defineNuxtPlugin((nuxtApp: {
    vueApp: { use: (arg0: any) => void; config: { globalProperties: { $toast: any } } };
    provide: (arg0: string, arg1: any) => void
}) => {
    nuxtApp.vueApp.use(ToastService)

    nuxtApp.provide('toast', nuxtApp.vueApp.config.globalProperties.$toast)
})
