<script lang="ts">
import {useAuthStore} from "~/stores/auth";
import {facultyApi} from "~/api/FacultyAPI";
import faculty from "~/pages/admin/faculty.vue";

type NavItem = { label: string; to: string }

export default defineComponent({
    data() {
        return {
            authStore: useAuthStore(),
            organization: '',
            role: '',
        }
    },
    computed: {
        navItems(this: any): NavItem[] {
            const role = this.authStore?.user?.user_type
            if (!role) {
                return [
                    {label: 'Help', to: '/help'}
                ]
            }

            if (role === 'student') {
                return [
                    {label: 'Dashboard', to: '/student'},
                    {label: 'Registration', to: '/student/registration'},
                    {label: 'Courses', to: '/student/courses'},
                    {label: 'Student Records', to: '/student/records'},
                    {label: 'Help', to: '/help'},
                ]
            }

            if (role === 'faculty') {
                return [
                    {label: 'Dashboard', to: '/faculty'},
                    {label: 'Courses', to: '/faculty/courses'},
                    {label: 'Advisees', to: '/faculty/advisees'},
                    {label: 'Gradebook', to: '/faculty/gradebook'},
                    {label: 'Help', to: '/help'},
                ]
            }

            if (role === 'admin') {
                return [
                    {label: 'Dashboard', to: '/admin'},
                    {label: 'Manage Courses', to: '/admin/courses'},
                    {label: 'Manage Faculty', to: '/admin/faculty'},
                    {label: 'Manage Organizations', to: '/admin/organizations'},
                    {label: 'Manage Students', to: '/admin/students'},
                    {label: 'Help', to: '/help'},
                ]
            }

            return [{label: 'Help', to: '/help'}]
        }
    },
    watch: {
        'authStore.user': {
            immediate: true,
            handler() {
                this.getRoles()
            }
        }
    },
    methods: {
        async handleLogout(this: any) {
            await this.authStore.logout()
            navigateTo('/login')
        },

        async getRoles(this: any) {
            if (this.authStore?.user?.user_type == 'faculty') {
                const faculty = await facultyApi.find(this.authStore?.user?.id)
                this.role = faculty?.role_type || ''
                this.role = this.role.charAt(0).toUpperCase() + this.role.slice(1)
            } else {
                this.role = this.authStore.user.user_type.charAt(0).toUpperCase() + this.authStore.user.user_type.slice(1)
            }
        }
    },
})
</script>

<template>
    <!-- nav bar background -->
    <nav class="bg-white shadow-md border-b border-gray-200">
        <div class="flex justify-between items-center py-1 px-4">
            <!-- navigation links -->
            <div class="flex space-x-1">
                <NuxtLink
                    v-for="item in navItems"
                    :key="item.to"
                    :to="item.to"
                    class="px-6 py-3 text-sm font-medium border rounded-md text-gray-700 border-gray-300"
                    active-class="bg-blue-600 text-white border-blue-600">
                    {{ item.label }}
                </NuxtLink>
            </div>

            <!-- user and logout -->
            <div class="flex items-center space-x-4">
                <span class="text-gray-700">{{ role || '' }} - {{ authStore?.fullName || '' }}</span>
                <button
                    @click="handleLogout"
                    class="px-4 py-2 text-sm font-medium text-red-600 border border-red-300 rounded-md hover:bg-red-50 hover:text-red-700">
                    Logout
                </button>
            </div>
        </div>
    </nav>
</template>
