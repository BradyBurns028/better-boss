<script lang="ts">
import {type PlanOfStudy, planOfStudyApi} from "~/api/PlanOfStudyAPI";
import {plannedCourseApi} from "~/api/PlannedCourseAPI";

export default defineNuxtComponent({
    name: 'StudentCoursesPlans',
    data() {
        return {
            plan: null as any,
            savingByCourse: {} as Record<number, boolean>,
            selections: {} as Record<number, number | null>,
            originals: {}  as Record<number, number | null>,
            editingRows: ref([]),
            TERM_ORDER_DESC: ['Fall', 'Summer', 'Spring', 'Winter'],
            YEAR_OPTIONS: [2030, 2029, 2028, 2027, 2026, 2025]
        }
    },
    setup() {
        definePageMeta({
            layout: 'student-header'
        })
    },
    async mounted() {
        await this.getPlanOfStudy()
    },
    methods: {
        async getPlanOfStudy(this: any) {
            this.plan = await planOfStudyApi.list(1, 1)

            const courses = this.plan?.courses || []
            courses.forEach((c: any) => {
                const id = c.id
                const current = c.plan?.course_section_id ?? null
                this.originals[id]  = current
                this.selections[id] = current
            })
        },
        termRank(this: any, t: string): number {
            const i = this.TERM_ORDER_DESC.indexOf(t as any) as number
            return i === -1 ? this.TERM_ORDER_DESC.length : i
        },
        sectionOptionLabel(s: any) {
            const time = s.time ? s.time.slice(0, 5) : '--:--'
            return `Sec ${s.section_number} • ${time} • ${s.room_number}`
        },
        async onRowEditSave(this: any, event: any) {
            await plannedCourseApi.create({
                term: event.newData.planTerm,
                year: event.newData.planYear,
                course_section_id: event.newData.selectedSectionId,
                plan_of_study_id: event.newData.planId,
                course_id: event.newData.courseId
            })
            await this.getPlanOfStudy()
        },
        async removeSection(this: any, event: any) {
            await plannedCourseApi.create({
                term: null,
                year: null,
                course_section_id: null,
                plan_of_study_id: event.planId,
                course_id: event.courseId
            })
            await this.getPlanOfStudy()
        },
    },
    computed: {
        rows(this: any): any[] {
            if (!this.plan) return []
            return (this.plan[0]?.courses || []).map((c: any) => {
                const term = c.plan?.term
                const year = c.plan?.year
                const eligible = (c.sections || []).filter((s: any) => s.term === term && s.year === year)
                const groupSortKey = (year || 0) * 100 + (100 - this.termRank(term))
                const eligibleOptions = eligible.map((s: any) => ({
                    label: this.sectionOptionLabel(s),
                    value: s.id,
                }))
                return {
                    planId: this.plan[0].id,
                    courseId: c.id,
                    courseCode: c.course_code,
                    name: c.name,
                    credits: c.credits,
                    planTerm: term,
                    planYear: year,
                    groupKey: `${year} • ${term}`,
                    groupSortKey,
                    allSections: c.sections || [],
                    eligibleSections: eligible,
                    eligibleOptions,
                    selectedSectionId: c.plan?.course_section_id ?? null,
                }
            })
        },
        sortedRows(): any[] {
            return [...this.rows].sort((a, b) => {
                if (a.groupSortKey !== b.groupSortKey) return b.groupSortKey - a.groupSortKey
                return String(a.courseCode).localeCompare(String(b.courseCode))
            })
        },
    }
});
</script>
<template>
    <div class="p-4">
        <DataTable
            v-model:editing-rows="editingRows"
            :value="sortedRows"
            editMode="row"
            rowGroupMode="subheader"
            groupRowsBy="groupKey"
            @row-edit-save="onRowEditSave"
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

            <Column field="credits" header="Credits">
                <template #body="{ data }">
                    <span class="px-2 py-1 rounded bg-surface-200 dark:bg-surface-700">
                        {{ data.credits }}
                    </span>
                </template>
            </Column>

            <Column header="Section">
                <template #body="{ data }">
                    <div v-if="data.eligibleOptions.find(o => Number(o.value) === Number(data.selectedSectionId))?.label">
                        {{ data.eligibleOptions.find(o => Number(o.value) === Number(data.selectedSectionId))?.label }}
                    </div>
                    <div v-else class="text-xs opacity-70 italic">
                        No section selected
                    </div>
                </template>
                <template #editor="{ data }">
                    <div class="flex items-center gap-2">
                        <UiBaseSelect
                            v-model="data.selectedSectionId"
                            :options="data.eligibleOptions"
                            optionLabel="label"
                            option-value="value"
                            placeholder="Select section"
                            :disabled="!data.eligibleSections.length || savingByCourse[data.courseId]"
                            class="min-w-64"
                            :show-label="false"
                            :clearable="true"
                        />
                        <UiBaseSelect
                            v-model="data.planTerm"
                            placeholder="Change Term"
                            :options="TERM_ORDER_DESC"
                            :show-label="false"
                        />
                        <UiBaseSelect
                            v-model="data.planYear"
                            placeholder="Change Year"
                            :options="YEAR_OPTIONS"
                            :show-label="false"
                        />
                        <Button
                            size="small"
                            label="Remove"
                            severity="secondary"
                            outlined
                            :loading="!!savingByCourse[data.courseId]"
                            @click="removeSection(data)"
                        />
                    </div>
                </template>
            </Column>
            <Column
                :rowEditor="true"
                style="width: 10%; min-width: 8rem"
                bodyStyle="text-align:center"
            ></Column>
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