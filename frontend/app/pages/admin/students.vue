<script lang="ts">
import { userApi, type User } from '~/api/UserAPI'
import BaseDataTable from '~/components/ui/BaseDataTable.vue'

definePageMeta({
  layout: 'student-header'
})

export default {
  components: { BaseDataTable},

  data() {
    return {
      students: [] as User[]
    }
  },

  methods: {
    async fetchStudents() {
      try {
        const result = await userApi.list(1, 1000)
        if (Array.isArray(result)) {
          this.students = result.filter(user => user.user_type === 'student')
        }
      } catch (error) {
      }
    }
  },

  mounted() {
    this.fetchStudents()
  }
}
</script>

<template>
  <div class="p-6">
    <BaseDataTable
      header="Students"
      :tableValue="students"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
    >
      <Column field="id" header="Id" />
      <Column field="first_name" header="First Name" />
      <Column field="last_name" header="Last Name" />
      <Column field="email" header="Email" />

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No students found.
        </div>
      </template>
    </BaseDataTable>
  </div>
</template>
