<script lang="ts">
import {courseApi} from "~/api/CourseAPI";
import {facultyApi} from "~/api/FacultyAPI";

export default defineNuxtComponent({
    name: 'StudentCourses',
    data() {
        return {
            loading: true,
            courses: {},
            search: '',
            activeTerm: null as unknown as number,
            activeYear: null as unknown as string,
            activeInstructorId: null as unknown as number,
            yearOptions: [2030, 2029, 2028, 2027, 2026, 2025, 2024, 2023, 2022],
            termOptions: ['Fall', 'Winter', 'Spring', 'Summer'],
            instructors: []
        }
    },
    setup() {
        definePageMeta({
            layout: 'student-header'
        })
    },
    mounted() {
        this.getCourses()
        this.getInstructors()
    },
    methods: {
        async getCourses(this: any) {
            this.loading = true;
            const params: Record<string, any> = {}
            const needle = this.search.trim()

            if (needle.length > 0) {
                params['matches[like]'] = needle
            }
            if (this.activeTerm) {
                params['term[eq]'] = this.activeTerm
            }
            if (this.activeYear) {
                params['year[eq]'] = this.activeYear
            }
            if (this.activeInstructorId) {
                params['instructor_id[eq]'] = this.activeInstructorId
            }

            this.courses = await courseApi.list(1, 1000, params)
            this.loading = false;
        },
        async getInstructors() {
            const raw = await facultyApi.all()
            this.instructors = raw?.map((f: any) => ({
                id: f.id,
                name: `${f.user?.first_name ?? ''} ${f.user?.last_name ?? ''}`.trim(),
            }))
        },
        onRowClick(event: { data?: any }) {
            const id = event?.data?.id
            if (!id) return
            navigateTo(`/student/courses/${id}`)
        },
    },
    watch: {
        search() {
            this.getCourses()
        },
        activeTerm() {
            this.getCourses()
        },
        activeYear() {
            this.getCourses()
        },
        activeInstructorId() {
            this.getCourses()
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
            rowHover
            @row-click="onRowClick"
            :rowClass="() => 'cursor-pointer'"
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
                        <UiBaseSelect
                            v-model="activeTerm"
                            size="small"
                            :show-label="false"
                            placeholder="Term"
                            :options="termOptions"
                            :clearable="true"
                        />
                        <UiBaseSelect
                            v-model="activeYear"
                            size="small"
                            :show-label="false"
                            placeholder="Year"
                            :options="yearOptions"
                            :clearable="true"
                        />
                        <UiBaseSelect
                            v-model="activeInstructorId"
                            size="small"
                            :show-label="false"
                            placeholder="Instructor"
                            :options="instructors"
                            option-label="name"
                            option-value="id"
                            :filter="true"
                            :clearable="true"
                        />
                    </div>
                </div>
            </template>
            <Column
                field="course_code"
                header="Course Code"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                field="name"
                header="Course Name"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                field="credits"
                header="Credits"
                body-class="whitespace-nowrap text-right"
                sortable
            />
            <Column
                header="Prerequisite"
                body-class="whitespace-nowrap"
                sortable
            >
                <template #body="{ data }">
                  <span>
                    {{ data.prerequisite?.course_code ?? '—' }}
                  </span>
                </template>
            </Column>
            <Column
                header="Description"
                bodyClass="max-w-80"
                sortable
            >
                <template #body="{ data }">
                    <div class="min-w-0 overflow-hidden text-ellipsis whitespace-nowrap">
                        {{ data.description ?? '—' }}
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>