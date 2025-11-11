<script lang="ts">
import {degreeProgramApi} from "~/api/DegreeProgramAPI";

export default defineNuxtComponent({
  name: 'DegreePrograms',
  data() {
    return {
      loading: true,
      degreePrograms: [],
      search: '',
    }
  },

  setup() {
    definePageMeta({
      layout: 'student-header'
    })
  },
  mounted() {
    this.getDegreePrograms()
  },
  methods: {
    async getDegreePrograms(this: any) {
      this.loading = true;
      const params: Record<string, any> = {}
      const needle = this.search.trim()

      if (needle.length > 0) {
        params['matches[like]'] = needle
      }

      this.degree_program = await degreeProgramApi.list(1, 1000, params)
      this.loading = false;
    },
    onRowClick(event: { data?: any }) {
      const id = event?.data?.id
      if (!id) return
      navigateTo(`/student/degree_programs/${id}`)
    },
  },
  watch: {
    search() {
      this.getDegreePrograms()
    }
  }
})
</script>
<template>
  <div class ="p-4 w-full">
    <DataTable
      :value="degreePrograms"
      dataKey="id"
      :loading="loading"
      class="w-full"
      tableStyle="table-layout: auto"
      row-hover
      @row-click = "onRowClick"
      :row-class="() => 'cursor-pointer'"
    >
      <template #header>
        <div class="flex flex-row gap-2 items-center justify-between">
          <h4 class="m-0">Courses</h4>
          <div class="flex flex-row gap-2 items-center">
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText size="small" v-model="search" placeholder="Search..." />
            </IconField>
          </div>
        </div>
      </template>
      <Column
        field="name"
        header="Degree Program"
        header-class="whitespace-nowrap"
        body-class="whitespace-nowrap"
        sortable
      />
      <Column
        field="department"
        header="Department"
        header-class="whitespace-nowrap"
        body-class="whitespace-nowrap"
        sortable
      />
      <Column
        field="program_chair"
        header="Program Chair"
        header-class="whitespace-nowrap"
        body-class="whitespace-nowrap"
        sortable
      />
    </DataTable>
  </div>
</template>
