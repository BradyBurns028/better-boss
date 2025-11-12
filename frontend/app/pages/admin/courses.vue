<script lang="ts">
import { courseApi, type Course } from '~/api/CourseAPI'
import BaseDataTable from '~/components/ui/BaseDataTable.vue'
import BaseDialog from '~/components/ui/BaseDialog.vue'
import { useToast } from 'primevue/usetoast'

definePageMeta({
  layout: 'student-header'
})

export default {
  components: { BaseDataTable, BaseDialog },

  data() {
    return {
      courses: [] as Course[],
      search: '',
      showCreateDialog: false,
      newCourse: {
        course_code: '',
        name: '',
        credits: 0,
        prerequisite_id: null as number | null,
        description: '',
        department_id: null as number | null
      },
      toast: useToast()
    }
  },

  computed: {
    filteredCourses(): Course[] {
      const term = this.search.toLowerCase()
      return this.courses.filter(course =>
        course.course_code.toLowerCase().includes(term) ||
        course.name.toLowerCase().includes(term) ||
        (course.description ?? '').toLowerCase().includes(term)
      )
    }
  },

  methods: {
    async fetchCourses() {
      try {
        const result = await courseApi.list(1, 1000)
        if (Array.isArray(result)) {
          this.courses = result
        }
      } catch (error) {
        console.error(error)
      }
    },

    async createCourse() {
      try {
        if (!this.newCourse.department_id) {
          this.toast.add({
            severity: 'warn',
            summary: 'Warning',
            detail: 'Department ID is required',
            life: 3000
          })
          return
        }

        await courseApi.create(this.newCourse)
        this.toast.add({
          severity: 'success',
          summary: 'Created',
          detail: 'Course created successfully',
          life: 3000
        })
        this.showCreateDialog = false
        this.resetForm()
        await this.fetchCourses()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to create course',
          life: 3000
        })
      }
    },

    async deleteCourse(id: number) {
      try {
        await courseApi.destroy(id)
        await this.fetchCourses()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to delete course',
          life: 3000
        })
      }
    },

    resetForm() {
      this.newCourse = {
        course_code: '',
        name: '',
        credits: 0,
        prerequisite_id: null,
        description: '',
        department_id: null
      }
    }
  },

  mounted() {
    this.fetchCourses()
  }
}
</script>

<template>
  <div class="p-6">
    <!-- Data Table -->
    <BaseDataTable
      :tableValue="filteredCourses"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
    >
      <!-- Header with title, search, add button -->
      <template #header>
        <div class="flex justify-between items-center w-full">
          <h2 class="text-xl font-semibold">Courses</h2>
          <div class="flex items-center gap-2">
            <InputText
              v-model="search"
              placeholder="Search by code, name, or description..."
              class="p-inputtext-sm w-64"
            />
            <Button
              label="Add Course"
              icon="pi pi-plus"
              class="p-button-success"
              @click="showCreateDialog = true"
            />
          </div>
        </div>
      </template>

      <!-- Columns -->
      <Column field="course_code" header="Course Code" sortable />
      <Column field="name" header="Course Name" sortable />
      <Column field="credits" header="Credits" sortable />
      <Column header="Prerequisite" sortable>
        <template #body="{ data }">
          {{ data.prerequisite_id ? 'YES' : '—' }}
        </template>
      </Column>
      <Column field="description" header="Description" sortable>
        <template #body="{ data }">
          <div class="min-w-0 break-words">{{ data.description ?? '—' }}</div>
        </template>
      </Column>
      <Column header="Actions">
        <template #body="{ data }">
          <Button
            label="Delete"
            icon="pi pi-trash"
            class="p-button-danger p-button-sm"
            @click="deleteCourse(data.id)"
          />
        </template>
      </Column>

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No courses found.
        </div>
      </template>
    </BaseDataTable>

    <!-- Create Course Dialog -->
    <BaseDialog
      v-model:visible="showCreateDialog"
      header="Create New Course"
      size="medium"
      :modal="true"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block mb-1 font-medium">Course Code</label>
          <InputText v-model="newCourse.course_code" placeholder="Course code" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Course Name</label>
          <InputText v-model="newCourse.name" placeholder="Course name" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Credits</label>
          <InputNumber v-model="newCourse.credits" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Prerequisite ID</label>
          <InputNumber v-model="newCourse.prerequisite_id" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Description</label>
          <InputText v-model="newCourse.description" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Department ID</label>
          <InputNumber v-model="newCourse.department_id" class="w-full" />
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <Button label="Cancel" class="p-button-text" @click="showCreateDialog = false" />
          <Button label="Create" icon="pi pi-check" class="p-button-success" @click="createCourse" />
        </div>
      </div>
    </BaseDialog>
  </div>
</template>
