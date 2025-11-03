<script lang="ts">
import { organizationApi, type Organization } from '~/api/OrganizationAPI'
import BaseDataTable from '~/components/ui/BaseDataTable.vue'

definePageMeta({
  layout: 'student-header'
})

export default {
  components: { BaseDataTable},

  data() {
    return {
      organizations: [] as Organization[]
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
    <BaseDataTable
      header="Organizations"
      :tableValue="organizations"
      :paginator="true"
      :rows="10"
      :stripedRows="true"
      dataKey="id"
    >
      <Column field="name" header="Organization Name" />
      <Column field="address" header="Address" />

      <template #empty>
        <div class="p-4 text-center text-gray-400 italic">
          No organizations found.
        </div>
      </template>
    </BaseDataTable>
  </div>
</template>
