<script lang="ts">
import {studentApi} from "~/api/StudentAPI";
import {useAuthStore} from "~/stores/auth";
import {userApi} from "~/api/UserAPI";

export default defineNuxtComponent({
    name: 'StudentCoursesPlans',
    data() {
        return {
            enrollments: null as any,
            savingByCourse: {} as Record<number, boolean>,
            selections: {} as Record<number, number | null>,
            originals: {}  as Record<number, number | null>,
            editingRows: ref([]),
            TERM_ORDER_DESC: ['Fall', 'Summer', 'Spring', 'Winter'],
            YEAR_OPTIONS: [2030, 2029, 2028, 2027, 2026, 2025, 2024, 2023, 2022]
        }
    },
    setup() {
        definePageMeta({
            layout: 'student-header'
        })
    },
    async mounted() {
        await this.getEnrollments()
    },
    methods: {
        async getEnrollments(this: any) {
            const studentId = (await userApi.find(useAuthStore()?.user.id))?.student.id
            if (studentId) {
                const student = await studentApi.find(studentId)
                if (student) {
                    this.enrollments = student.enrollments
                }
            }
        },
        termRank(this: any, t: string): number {
            const i = this.TERM_ORDER_DESC.indexOf(t as any) as number
            return i === -1 ? this.TERM_ORDER_DESC.length : i
        },
        sectionOptionLabel(s: any) {
            const time = s.time ? s.time.slice(0, 5) : '--:--'
            return `Sec ${s.section_number} • ${time} • ${s.room_number}`
        },
    },
    computed: {
        rows(this: any): any[] {
            if (!this.enrollments) return []
            return (this.enrollments).map((enrollment: any) => {
                const section = enrollment.course_section
                const course = enrollment.course
                console.log(section);
                const term = section.term
                const year = section.year
                const groupSortKey = (year || 0) * 100 + (100 - this.termRank(term))
                return {
                    courseId: section.id,
                    courseCode: course.course_code,
                    name: course.name,
                    credits: course.credits,
                    planTerm: term,
                    planYear: year,
                    sectionOptionLabel: this.sectionOptionLabel(section),
                    instructor: `${section.instructor.user.first_name} ${section.instructor.user.last_name}`,
                    groupKey: `${year} • ${term}`,
                    groupSortKey,
                    grade: enrollment.grade
                }
            })
        },
        sortedRows(): any[] {
            return [...this.rows].sort((a, b) => {
                if (a.groupSortKey !== b.groupSortKey) return b.groupSortKey - a.groupSortKey
                return String(a.courseCode).localeCompare(String(b.courseCode))
            })
        }
    }
})
</script>
<template>
    <div class="p-4">
        <DataTable
            :value="sortedRows"
            editMode="row"
            rowGroupMode="subheader"
            groupRowsBy="groupKey"
            :pt="{
                rowGroupHeader: { class: 'bg-surface-100 dark:bg-surface-800' },
                rowGroupHeaderCell: { colspan: 1000 },
            }"
        >
            <template #empty> No courses planned. </template>
            <template #groupheader="{ data }">
                <div class="flex items-center justify-between py-2 px-3 font-semibold">
                    <span>{{ data.groupKey }}</span>
                </div>
            </template>

            <Column field="courseCode" header="Course">
                <template #body="{ data }">
                    <div class="font-medium">{{ data.courseCode }}</div>
                    <div class="text-sm opacity-80">{{ data.name }}</div>
                </template>
            </Column>
            <Column header="Section">
                <template #body="{ data }">
                    <div class="font-medium">{{ data.sectionOptionLabel }}</div>
                </template>
            </Column>

            <Column field="credits" header="Credits">
                <template #body="{ data }">
                    <span class="px-2 py-1 rounded bg-surface-200 dark:bg-surface-700">
                        {{ data.credits }}
                    </span>
                </template>
            </Column>
            <Column header="Instructor">
                <template #body="{ data }">
                    <div class="font-medium">{{ data.instructor }}</div>
                </template>
            </Column>
            <Column header="Grade">
                <template #body="{ data }">
                    <div v-if="data.grade">
                        {{ data.grade }}
                    </div>
                    <div v-else class="text-xs opacity-70 italic">
                        No grade
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<style scoped>
/* make subheaders “sticky” so the group label stays visible while scrolling */
:deep(.p-datatable-tbody > tr.p-rowgroup-header) {
    position: sticky;
    top: 0;
    z-index: 1;
}
</style>