<!-- student header layout with organization name header and navigation -->
<script lang="ts">
import {useAuthStore} from "~/stores/auth";
import {userApi} from "~/api/UserAPI";

type NavItem = { label: string; to: string }

export default defineComponent({
    data() {
        return {
            authStore: useAuthStore(),
            organization: ''
        }
    },
    methods: {
        async getOrganization(this: any) {
            const user = await userApi.find(this.authStore.user.id)
            if (!user) this.organization = 'Something went wrong'
            if (user.user_type === 'student') {
                this.organization = user.student.organization.name
            } else if (user.user_type === 'faculty') {
                this.organization = user.organization.name
            }
        },
    },
    mounted() {
        this.getOrganization()
    }
})
</script>
<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <!-- organization name header -->
    <header class="bg-blue-600 text-white text-center py-10 text-4xl font-bold">{{organization}}</header>
    <!-- navigation bar below the header -->
    <Nav />
    <!-- page content slot -->
    <slot />
  </div>
</template>
