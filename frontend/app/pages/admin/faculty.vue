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
      faculties: [] as User[],
      search: '',
      showCreateDialog: false,
      newFaculty: {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        user_type: 'faculty'
      },
      toast: useToast()
    }
  },

  computed: {
    filteredFaculties(): User[] {
      const term = this.search.toLowerCase()
      return this.faculties.filter(faculty =>
        faculty.first_name.toLowerCase().includes(term) ||
        faculty.last_name.toLowerCase().includes(term) ||
        faculty.email.toLowerCase().includes(term)
      )
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
        console.error(error)
      }
    },

    async createFaculty() {
      try {
        await userApi.create(
          { ...this.newFaculty, password_confirmation: this.newFaculty.password },
          'Faculty'
        )
        this.toast.add({
          severity: 'success',
          summary: 'Created',
          detail: 'Faculty created successfully',
          life: 3000
        })
        this.showCreateDialog = false
        this.resetForm()
        await this.fetchFaculties()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to create faculty',
          life: 3000
        })
      }
    },

    async deleteFaculty(id: number) {
      try {
        await userApi.destroy(id, 'Faculty')
        this.toast.add({
          severity: 'success',
          summary: 'Deleted',
          detail: 'Faculty deleted successfully',
          life: 3000
        })
        await this.fetchFaculties()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to delete faculty',
          life: 3000
        })
      }
    },

    resetForm() {
      this.newFaculty = {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        user_type: 'faculty'
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
    <!-- Data Table -->
    <BaseDataTable
      :tableValue="filteredFaculties"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
    >
      <!-- Header with title, search, add button -->
      <template #header>
        <div class="flex justify-between items-center w-full">
          <h2 class="text-xl font-semibold">Faculty</h2>
          <div class="flex items-center gap-2">
            <InputText
              v-model="search"
              placeholder="Search by name or email..."
              class="p-inputtext-sm w-64"
            />
            <Button
              label="Add Faculty"
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
        <template #body="{ data }">
          <Button
            label="Delete"
            icon="pi pi-trash"
            class="p-button-danger p-button-sm"
            @click="deleteFaculty(data.id)"
          />
        </template>
      </Column>

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No faculty found.
        </div>
      </template>
    </BaseDataTable>

    <!-- Create Faculty Dialog -->
    <BaseDialog
      v-model:visible="showCreateDialog"
      header="Create New Faculty"
      size="medium"
      :modal="true"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block mb-1 font-medium">First Name</label>
          <InputText v-model="newFaculty.first_name" placeholder="First name" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Last Name</label>
          <InputText v-model="newFaculty.last_name" placeholder="Last name" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Email</label>
          <InputText v-model="newFaculty.email" placeholder="Email" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Password</label>
          <Password
            v-model="newFaculty.password"
            placeholder="Password"
            toggleMask
            class="w-full"
          />
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <Button label="Cancel" class="p-button-text" @click="showCreateDialog = false" />
          <Button label="Create" icon="pi pi-check" class="p-button-success" @click="createFaculty" />
        </div>
      </div>
    </BaseDialog>
  </div>
</template>
