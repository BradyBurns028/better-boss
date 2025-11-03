<script lang="ts">
import { courseApi, type Course } from '~/api/CourseAPI'
import BaseDataTable from '~/components/ui/BaseDataTable.vue'

definePageMeta({
  layout: 'student-header'
})

export default {
  components: { BaseDataTable },

  data() {
    return {
      courses: [] as Course[],
      loading: true,
    }
  },

  methods: {
    async fetchCourses() {
      this.loading = true
      try {
        const result = await courseApi.list(1, 1000)
        if (Array.isArray(result)) {
          this.courses = result
        }
      } catch (error) {
        console.error(error)
      }
      this.loading = false
    }
  },

  mounted() {
    this.fetchCourses()
  }
}
</script>

<template>
  <div class="p-6">
    <BaseDataTable
      header="Courses"
      :tableValue="courses"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
      :loading="loading"
    >
      <Column field="course_code" header="Course Code" />
      <Column field="name" header="Course Name" />
      <Column field="credits" header="Credits" />
      <Column field="prerequisite_id" header="Prerequisite">
        <template #body="{ data }">
          {{ data.prerequisite_id ? 'YES' : '—' }}
        </template>
      </Column>
      <Column field="description" header="Description">
        <template #body="{ data }">
          <div class="min-w-0 overflow-hidden text-ellipsis whitespace-nowrap">
            {{ data.description ?? '—' }}
          </div>
        </template>
      </Column>

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No courses found.
        </div>
      </template>
    </BaseDataTable>
  </div>
</template>
