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
      faculties: [] as User[]
    }
  },

  methods: {
    async fetchFaculties() {
      try {
        const result = await userApi.list(1, 1000)
        if (Array.isArray(result)) {
          this.faculties = result.filter(user => user.user_type === 'faculty')
        }
      } catch (error) {
      }
    }
  },

  mounted() {
    this.fetchFaculties()
  }
}
</script>

<template>
  <div class="p-6">
    <BaseDataTable
      header="Faculty"
      :tableValue="faculties"
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
          No faculty found.
        </div>
      </template>
    </BaseDataTable>
  </div>
</template>
