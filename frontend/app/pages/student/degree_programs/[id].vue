<script lang="ts">
import {type DegreeProgram, degreeProgramApi} from "~/api/DegreeProgramAPI";

export default defineComponent({
  name: "ViewDegreeProgram",
  data() {
    return {
      loading: true,
      activeDegreeProgramId: null as unknown as number,
      activeDegreeProgram: [],
      search: '',
    }
  },
  setup() {
    definePageMeta({
      layout: 'student-header'
    })
  },
  async mounted(){
    this.activeDegreeProgramId = this.$route.params.id
    await this.getDegreeProgram()
  },
  methods: {
    async getDegreeProgram(this: any) {
      this.loading = true;
      this.activeDegreeProgram = (await degreeProgramApi.find(this.activeDegreeProgramId)) as DegreeProgram

      if (Array.isArray(this.activeDegreeProgram.requirements)) {
        this.activeDegreeProgram.requirements = this.activeDegreeProgram.requirements.map((r: any, i: number) => ({
          ...r,
          _dtKey: r.course_code ? `${r.course_code}-${i}` : `req-${i}`
        }));
      }

      this.loading = false;
    },
  }
})
</script>
<template>
  <div class="w-full flex flex-col md:flex-row gap-4 px-4 pt-4">
    <div class="flex-1 p-4 bg-white rounded shadow">
      <h2 class="text-lg font-semibold mb-2">{{ activeDegreeProgram?.name }}</h2>
      <p class="text-md mb-2">Department: {{ activeDegreeProgram?.department?.name }}</p>
    </div>
    <div class="flex-1 p-4 bg-white rounded shadow">
      <h2 class="text-lg font-semibold mb-2">Program Chair</h2>
      <p class="text-md mb-2">Name: {{ activeDegreeProgram?.program_chair?.user
          ? `${activeDegreeProgram.program_chair.user.first_name} ${activeDegreeProgram.program_chair.user.last_name}`
          : '-'
        }}
      </p>
      <p class="text-md mb-2">Email: {{ activeDegreeProgram?.program_chair?.user?.email }}</p>
      <p class="text-md mb-2">office: {{ activeDegreeProgram?.program_chair?.office }}</p>
    </div>
  </div>
  <DataTable
    :value="activeDegreeProgram?.requirements"
    dataKey="_dtKey"
    :loading="loading"
    class="w-full mt-4 px-4"
    tableStyle="table-layout: auto"
  >
    <template #header>
      <div class="flex flex-row gap-2 items-center justify-between">
        <h4 class="m-0">Requirements</h4>
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
      field="course.course_code"
      header="Course Code"
      header-class="whitespace-nowrap"
      body-class="whitespace-nowrap"
      sortable
    />
    <Column
      field="course.name"
      header="Course Name"
      header-class="whitespace-nowrap"
      body-class="whitespace-nowrap"
      sortable
    />
    <Column
      field="course.description"
      header="Description"
      header-class="whitespace-nowrap"
      body-class="whitespace-nowrap"
      sortable
    />
    <Column
      field="course.credits"
      header="Credits"
      header-class="whitespace-nowrap"
      body-class="whitespace-nowrap"
      sortable
    />
    <Column
      field="minimum_grade"
      header="Grade Required"
      header-class="whitespace-nowrap"
      body-class="whitespace-nowrap"
      sortable
    />
  </DataTable>
</template>

<style scoped>

</style>