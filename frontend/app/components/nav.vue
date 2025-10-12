<script lang="ts">
import {useAuthStore} from "~/stores/auth";

export default defineComponent({
    data() {
        return {
            authStore: useAuthStore()
        }
    },
    methods: {
        async handleLogout(this: any) {
            await this.authStore.logout()
            navigateTo('login')
        }
    }
})
</script>
<template>
  <!-- nav bar background -->
  <nav class="bg-white shadow-md border-b border-gray-200">
    <div class="flex justify-between items-center py-1 px-4">
      <!-- navigation links -->
      <div class="flex space-x-1">
        <NuxtLink
          to="/dashboard"
          class="px-6 py-3 text-sm font-medium border rounded-md text-gray-700 border-gray-300"
          active-class="bg-blue-600 text-white border-blue-600"
          exact>Dashboard
        </NuxtLink>
        <NuxtLink
          to="/registration"
          class="px-6 py-3 text-sm font-medium border rounded-md text-gray-700 border-gray-300"
          active-class="bg-blue-600 text-white border-blue-600"
          exact>Registration
        </NuxtLink>
        <NuxtLink
          to="/courses"
          class="px-6 py-3 text-sm font-medium border rounded-md text-gray-700 border-gray-300"
          active-class="bg-blue-600 text-white border-blue-600"
          exact>Courses
        </NuxtLink>
        <NuxtLink
          to="/student-records"
          class="px-6 py-3 text-sm font-medium border rounded-md text-gray-700 border-gray-300"
          active-class="bg-blue-600 text-white border-blue-600"
          exact>Student Records
        </NuxtLink>
        <NuxtLink
          to="/help"
          class="px-6 py-3 text-sm font-medium border rounded-md text-gray-700 border-gray-300"
          active-class="bg-blue-600 text-white border-blue-600"
          exact>Help
        </NuxtLink>
      </div>
      <!-- user and logout -->
      <div class="flex items-center space-x-4">
        <span class="text-gray-700">first name last name</span>
        <button
          @click="handleLogout"
          class="px-4 py-2 text-sm font-medium text-red-600 border border-red-300 rounded-md hover:bg-red-50 hover:text-red-700">Logout
        </button>
      </div>
    </div>
  </nav>
</template>
