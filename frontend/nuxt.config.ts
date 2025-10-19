import { defineNuxtConfig } from 'nuxt/config'
import Aura from '@primeuix/themes/aura';

export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  autoImports: true,
  devServer: {
    host: '0.0.0.0',
    port: 3000
  },
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      baseURL: 'http://localhost',
    }
  },
  srcDir: 'app',
  css: ['~/assets/css/tailwind.css', 'primeicons/primeicons.css'],
  modules: [
    '@nuxtjs/tailwindcss',
    '@primevue/nuxt-module',
    '@pinia/nuxt'
  ],
  primevue: {
    importComponents: true,
    options: {
      theme: {
        preset: Aura,
        options: {
          prefix: 'p',
          darkModeSelector: 'system',
        }
      }
    },
    components: {
      exclude: [
        'Chart',
        'Editor',
        'Form',
        'FormField',
        'FormFieldGroup',
        'FormGroup',
        'FormItem',
        'Fieldset',
        'FieldGroup'
      ],
    }
  },
})
