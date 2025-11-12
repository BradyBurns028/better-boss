<script lang="ts">
import { userApi, type User } from '~/api/UserAPI'
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
      students: [] as User[],
      search: '',
      toast: useToast(),
      showCreateDialog: false,
      newStudent: {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        user_type: 'student'
      }
    }
  },

  computed: {
    filteredStudents() {
      const term = this.search.toLowerCase()
      return this.students.filter(
        s =>
          s.first_name.toLowerCase().includes(term) ||
          s.last_name.toLowerCase().includes(term) ||
          s.email.toLowerCase().includes(term)
      )
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
        console.error(error)
      }
    },

    async deleteStudent(id: number) {
      try {
        await userApi.destroy(id, 'Student')
        this.toast.add({
          severity: 'success',
          summary: 'Deleted',
          detail: 'Student deleted successfully',
          life: 3000
        })
        await this.fetchStudents()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to delete student',
          life: 3000
        })
      }
    },

    async createStudent() {
      try {
        await userApi.create(
          {
            ...this.newStudent,
            password_confirmation: this.newStudent.password
          },
          'Student'
        )
        this.toast.add({
          severity: 'success',
          summary: 'Created',
          detail: 'Student created successfully',
          life: 3000
        })
        this.showCreateDialog = false
        this.resetForm()
        await this.fetchStudents()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to create student',
          life: 3000
        })
      }
    },

    resetForm() {
      this.newStudent = {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        user_type: 'student'
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
    <!-- Data Table -->
    <BaseDataTable
      :tableValue="filteredStudents"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
    >
      <!-- Custom header with title and controls -->
      <template #header>
        <div class="flex justify-between items-center w-full">
          <h2 class="text-xl font-semibold">Students</h2>
          <div class="flex items-center gap-2">
            <InputText
              v-model="search"
              placeholder="Search by name or email..."
              class="p-inputtext-sm w-64"
            />
            <Button
              label="Add Student"
              icon="pi pi-plus"
              class="p-button-success"
              @click="showCreateDialog = true"
            />
          </div>
        </div>
      </template>

      <!-- Columns -->
      <Column field="id" header="Id" sortable />
      <Column field="first_name" header="First Name" sortable />
      <Column field="last_name" header="Last Name" sortable />
      <Column field="email" header="Email" sortable />

      <Column header="Actions">
        <template #body="slotProps">
          <Button
            label="Delete"
            icon="pi pi-trash"
            class="p-button-danger p-button-sm"
            @click="deleteStudent(slotProps.data.id)"
          />
        </template>
      </Column>

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No students found.
        </div>
      </template>
    </BaseDataTable>

    <!-- Create Student Dialog -->
    <BaseDialog
      v-model:visible="showCreateDialog"
      header="Create New Student"
      size="medium"
      :modal="true"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block mb-1 font-medium">First Name</label>
          <InputText v-model="newStudent.first_name" placeholder="First name" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Last Name</label>
          <InputText v-model="newStudent.last_name" placeholder="Last name" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Email</label>
          <InputText v-model="newStudent.email" placeholder="Email" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Password</label>
          <Password
            v-model="newStudent.password"
            placeholder="Password"
            toggleMask
            class="w-full"
          />
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <Button
            label="Cancel"
            class="p-button-text"
            @click="showCreateDialog = false"
          />
          <Button
            label="Create"
            icon="pi pi-check"
            class="p-button-success"
            @click="createStudent"
          />
        </div>
      </div>
    </BaseDialog>
  </div>
</template>
