<script lang="ts">
import {type Course, courseApi} from "~/api/CourseAPI";
import {planOfStudyApi} from "~/api/PlanOfStudyAPI";
import {useAuthStore} from "~/stores/auth";
import {plannedCourseApi} from "~/api/PlannedCourseAPI";

export default defineNuxtComponent({
    name: 'ViewCourse',
    data() {
        return {
            activeCourseId: null as unknown as number,
            activeCourse: [],
            showPlanOfStudy: false,
            yearOptions: [2025, 2024, 2023, 2022, 2021, 2020],
            termOptions: ['Fall', 'Winter', 'Spring', 'Summer'],
            planActiveYear: null as unknown as number,
            planActiveTerm: ''
        }
    },
    setup() {
        definePageMeta({
            layout: 'student-header'
        })
    },
    async mounted() {
        this.activeCourseId = this.$route.params.id
        await this.getCourse()
    },
    methods: {
        async getCourse(this: any) {
            this.activeCourse = (await courseApi.find(this.activeCourseId)) as Course
        },
        openPlanOfStudy(this: any) {
            this.showPlanOfStudy = !this.showPlanOfStudy
        },
        async addToPlannedCourses(this: any) {
            const auth = useAuthStore()

            const response = await planOfStudyApi.list(1, 1, {
                student_id: { eq: useAuthStore().user.student.id }
            })
            let planOfStudy = response[0] ?? null

            if (!planOfStudy) {
                planOfStudy = await planOfStudyApi.create({
                    student_id: auth.user.student.id,
                    degree_program_id: auth.user.student.degree_program.id
                })
            }

            if (!this.planActiveYear || this.planActiveTerm === '') {
                useNuxtApp().$toast.add({
                    severity: 'error',
                    summary: 'Missing field',
                    detail: 'You must provide a term and year',
                    life: 3000,
                })
                return
            }
            const plannedCourse = await plannedCourseApi.create({
                plan_of_study_id: planOfStudy.id,
                course_id: this.activeCourseId,
                year: this.planActiveYear,
                term: this.planActiveTerm,
                status: 'planned'
            });
            console.log(plannedCourse);
        }
    }
})
</script>
<template>
    <div class="flex-1 flex flex-col px-4 pt-4 space-y-6">
        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-lg font-semibold mb-2">{{ activeCourse?.name }}</h2>
            <p class="text-md mb-2">Course Code: {{ activeCourse?.course_code }}</p>
            <p class="text-md mb-2">{{ activeCourse?.description }}</p>
            <p class="text-md mb-2">Credits: {{ activeCourse?.credits }}</p>
            <p class="text-md mb-2">Department: {{ activeCourse?.department?.name }}</p>
            <p class="text-md mb-2" v-if="activeCourse?.prerequisite">Prerequisite: {{ activeCourse?.prerequisite.course_code }}</p>
        </div>
        <Button label="Add to Plan of Study" @click="openPlanOfStudy" />
        <DataTable
            :value="activeCourse.sections"
        >
            <template #header>
                <div class="flex flex-row gap-2 items-center justify-between">
                    <h4 class="m-0">Course Sections</h4>
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
                field="section_number"
                header="Section Number"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                field="term"
                header="Term"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                field="year"
                header="Year"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                field="capacity"
                header="Capacity"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                field="room_number"
                header="Room Number"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
            <Column
                header="Instructor"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
            >
                <template #body="{ data }">
                    {{ data?.instructor?.user
                    ? `${data.instructor.user.first_name} ${data.instructor.user.last_name}`
                    : '—'
                    }}
                </template>
            </Column>
        </DataTable>
        <UiBaseDialog
            v-model:visible="showPlanOfStudy"
            header="Add to Plan of Study"
            size="medium"
        >
            <div class="flex flex-row justify-between gap-x-4 mb-4">
                <UiBaseSelect
                    v-model="planActiveTerm"
                    placeholder="Term"
                    class="w-full"
                    :options="termOptions"
                />
                <UiBaseSelect
                    v-model="planActiveYear"
                    placeholder="Year"
                    class="w-full"
                    :options="yearOptions"
                />
            </div>
            <Button label="Add Course" @click="addToPlannedCourses" />
        </UiBaseDialog>
    </div>
</template>