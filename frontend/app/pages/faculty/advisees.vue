<script lang="ts">
import { studentApi, type Student } from '~/api/StudentAPI'

definePageMeta({
    layout: 'student-header'
})

export default defineNuxtComponent ({
    data() {
        return {
            loading: false as boolean,
            students: [] as Student[],

            // enroll dialog state
            showEnrollDialog: false as boolean,
            enrolling: false as boolean,
            yearOptions: [2028, 2027, 2026, 2025, 2024, 2023, 2022, 2021, 2020],
            termOptions: ['Fall', 'Winter', 'Spring', 'Summer'],
            selectedYear: null as number | null,
            selectedTerm: '' as string,
        }
    },
    methods: {
        async fetchStudents(this: any) {
            this.loading = true
            try {
                this.students = await studentApi.list(1, 1000)
            } catch (error) {
            } finally {
                this.loading = false
            }
        },

        openEnrollDialog(this: any) {
            this.showEnrollDialog = !this.showEnrollDialog
        },

        async enrollCurrentTerm(this: any) {
            this.enrolling = true

            const auth = useAuthStore()
            const headers: Record<string, string> = {}

            // If you use token-based auth anywhere, include token header as fallback.
            if ((auth as any)?.token) {
                headers['Authorization'] = `Bearer ${(auth as any).token}`
            }

            const summary = await $fetch('/api/enroll-current-term', {
                method: 'POST',
                body: {
                    term: this.selectedTerm,
                    year: this.selectedYear
                },
                credentials: 'include',
                headers
            })
            const detail = `Students have been enrolled for ${this.selectedTerm} ${this.selectedYear}`
            useNuxtApp().$toast.add({
                severity: 'success',
                summary: 'Courses enrolled',
                detail: detail,
                life: 3000,
            })

            this.openEnrollDialog()
        }
    },
    mounted() {
        this.fetchStudents()
    }
})
</script>

<template>
    <div class="p-4">
        <UiBaseDataTable
            header="Students"
            :tableValue="students"
            :paginator="true"
            :rows="10"
            :stripedRows="true"
            dataKey="id"
        >
            <template #header>
                <div class="flex flex-row gap-2 items-center justify-between">
                    <h4 class="m-0">Advisees</h4>
                    <Button
                        label="Enroll Advisees"
                        icon="pi pi-user-plus"
                        @click="openEnrollDialog"
                        size="small"
                    />
                </div>

            </template>
            <Column field="id" header="Student ID" />
            <Column field="user.first_name" header="First Name" />
            <Column field="user.last_name" header="Last Name" />
            <Column field="user.email" header="Email" />
            <Column field="degree_program.name" header="Degree Program" />

            <template #empty>
                <div class="p-4 text-center text-gray-400 italic">
                    No students found.
                </div>
            </template>
        </UiBaseDataTable>

        <UiBaseDialog
            v-model:visible="showEnrollDialog"
            header="Enroll Advisees for Term"
            size="medium"
        >
            <div class="flex flex-row justify-between gap-x-4 mb-4">
                <UiBaseSelect
                    v-model="selectedTerm"
                    placeholder="Term"
                    class="w-full"
                    :options="termOptions"
                />
                <UiBaseSelect
                    v-model="selectedYear"
                    placeholder="Year"
                    class="w-full"
                    :options="yearOptions"
                />
            </div>

            <div class="flex justify-between gap-x-4">
                <Button class="w-full h-10" label="Cancel" severity="secondary" @click="openEnrollDialog" :disabled="enrolling" />
                <Button class="w-full h-10 flex items-center justify-center" label="Enroll" icon="pi pi-check" @click="enrollCurrentTerm" :loading="enrolling" />
            </div>
        </UiBaseDialog>
    </div>
</template>
