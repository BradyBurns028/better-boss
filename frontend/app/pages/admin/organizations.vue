<script lang="ts">
import { organizationApi, type Organization } from '~/api/OrganizationAPI'
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
      organizations: [] as Organization[],
      search: '',
      showCreateDialog: false,
      newOrganization: {
        name: '',
        address: '',
        admin_id: ''
      },
      toast: useToast()
    }
  },

  computed: {
    filteredOrganizations(): Organization[] {
      const term = this.search.toLowerCase()
      return this.organizations.filter(org =>
        org.name.toLowerCase().includes(term) ||
        org.address.toLowerCase().includes(term)
      )
    }
  },

  methods: {
    async fetchOrganizations() {
      try {
        const result = await organizationApi.list(1, 1000)
        if (Array.isArray(result)) {
          this.organizations = result
        }
      } catch (error) {
        console.error(error)
      }
    },

    async createOrganization() {
      try {
        await organizationApi.create(this.newOrganization)
        this.toast.add({
          severity: 'success',
          summary: 'Created',
          detail: 'Organization created successfully',
          life: 3000
        })
        this.showCreateDialog = false
        this.resetForm()
        await this.fetchOrganizations()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to create organization',
          life: 3000
        })
      }
    },

    async deleteOrganization(id: number) {
      try {
        await organizationApi.destroy(id)
        this.toast.add({
          severity: 'success',
          summary: 'Deleted',
          detail: 'Organization deleted successfully',
          life: 3000
        })
        await this.fetchOrganizations()
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to delete organization',
          life: 3000
        })
      }
    },

    resetForm() {
      this.newOrganization = {
        name: '',
        address: '',
        admin_id: ''
      }
    }
  },

  mounted() {
    this.fetchOrganizations()
  }
}
</script>

<template>
  <div class="p-6">
    <!-- Data Table -->
    <BaseDataTable
      :tableValue="filteredOrganizations"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
    >
      <!-- Header with title, search, add button -->
      <template #header>
        <div class="flex justify-between items-center w-full">
          <h2 class="text-xl font-semibold">Organizations</h2>
          <div class="flex items-center gap-2">
            <InputText
              v-model="search"
              placeholder="Search by name or address..."
              class="p-inputtext-sm w-64"
            />
            <Button
              label="Add Organization"
              icon="pi pi-plus"
              class="p-button-success"
              @click="showCreateDialog = true"
            />
          </div>
        </div>
      </template>

      <!-- Columns -->
      <Column field="name" header="Organization Name" sortable />
      <Column field="address" header="Address" sortable />
      <Column header="Actions">
        <template #body="{ data }">
          <Button
            label="Delete"
            icon="pi pi-trash"
            class="p-button-danger p-button-sm"
            @click="deleteOrganization(data.id)"
          />
        </template>
      </Column>

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No organizations found.
        </div>
      </template>
    </BaseDataTable>

    <!-- Create Organization Dialog -->
    <BaseDialog
      v-model:visible="showCreateDialog"
      header="Create New Organization"
      size="medium"
      :modal="true"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block mb-1 font-medium">Organization Name</label>
          <InputText v-model="newOrganization.name" placeholder="Name" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Address</label>
          <InputText v-model="newOrganization.address" placeholder="Address" class="w-full" />
        </div>

        <div>
          <label class="block mb-1 font-medium">Admin ID</label>
          <InputText v-model="newOrganization.admin_id" placeholder="Admin ID" class="w-full" />
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <Button label="Cancel" class="p-button-text" @click="showCreateDialog = false" />
          <Button label="Create" icon="pi pi-check" class="p-button-success" @click="createOrganization" />
        </div>
      </div>
    </BaseDialog>
  </div>
</template>
