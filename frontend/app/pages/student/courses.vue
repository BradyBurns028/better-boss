<script setup lang="ts">
definePageMeta({
  layout: 'student-header'
})
</script>
<script lang="ts">
import {courseApi} from "~/api/CourseAPI";

export default defineNuxtComponent({
    name: 'StudentCourses',
    data() {
        return {
            loading: true,
            courses: {},
        }
    },
    mounted() {
        this.getCourses()
    },
    methods: {
        async getCourses(this: any) {
            this.loading = true;
            this.courses = await courseApi.list(1, 1000);
            this.loading = false;
        }
    }
})
</script>
<template>
    <div class="p-4 w-full">
        <DataTable
            :value="courses"
            dataKey="id"
            :loading="loading"
            class="w-full"
            tableStyle="table-layout: auto"
        >
            <Column field="course_code" header="Course Code" header-class="whitespace-nowrap" body-class="whitespace-nowrap" />
            <Column field="name" header="Course Name" header-class="whitespace-nowrap" body-class="whitespace-nowrap" />
            <Column field="credits" header="Credits" body-class="whitespace-nowrap text-right" />
            <Column header="Prerequisite" body-class="whitespace-nowrap">
                <template #body="{ data }">
                  <span>
                    {{ data.prerequisite_id ? 'YES' : '—' }}
                  </span>
                </template>
            </Column>
            <Column header="Description" bodyClass="max-w-80">
                <template #body="{ data }">
                    <div class="min-w-0 overflow-hidden text-ellipsis whitespace-nowrap">
                        {{ data.description ?? '—' }}
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>