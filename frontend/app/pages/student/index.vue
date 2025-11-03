<script lang="ts">
import {useAuthStore} from "~/stores/auth";
import {userApi} from "~/api/UserAPI";

definePageMeta({
    layout: 'student-header'
})

export default defineNuxtComponent({
    data() {
        return {
            authStore: useAuthStore(),
            advisor: '',
            degree_program: ''
        }
    },
    methods: {
      async getStudentInfo(this: any) {
        const user = await userApi.find(this.authStore.user.id)
        const advisorUser = user?.student.advisor.user;
        this.advisor = `${advisorUser.first_name} ${advisorUser.last_name}`

        this.degree_program = user?.student.degree_program.name;
      }
    },
    mounted() {
        this.getStudentInfo()
    }
})
</script>

<template>
<main class="flex-1 flex flex-col px-4 pt-4 space-y-6">
  <div class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-semibold mb-2">Student Information</h2>
    <p><strong>Advisor:</strong> {{ advisor }}</p>
    <p><strong>Degree Program:</strong> {{ degree_program }}</p>
  </div>
  <Notifications />
</main>
</template>
