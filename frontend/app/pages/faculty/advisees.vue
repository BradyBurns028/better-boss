<script lang="ts">
import { studentApi, type Student } from '~/api/StudentAPI'

definePageMeta({
    layout: 'student-header'
})

export default defineNuxtComponent ({
    data() {
        return {
            loading: false as boolean,
            students: [] as Student[]
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
        }
    },
    mounted() {
        this.fetchStudents()
    }
})
</script>

<template>
    <div class="p-6">
        <UiBaseDataTable
            header="Students"
            :tableValue="students"
            :paginator="true"
            :rows="10"
            :stripedRows="true"
            dataKey="id"
        >
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
    </div>
</template>
