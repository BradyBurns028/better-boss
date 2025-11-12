<script lang="ts">
import { useAuthStore } from "~/stores/auth";
import { courseApi } from "~/api/CourseAPI";
import {userApi} from "~/api/UserAPI";

export default defineNuxtComponent({
    name: "instructorHome",
    data() {
        return {
            loading: true,
            instructorId: null as unknown as number ,
            courses: [],
            sections: []
        }
    },
    setup() {
        definePageMeta({
            layout: 'student-header'
        })
    },
    mounted() {
        this.getCourseSections()
    },
    methods: {
        async getCourseSections(this: any) {
            this.loading = true;
            const params: Record<string, any> = {}

            const user = await userApi.find(useAuthStore()?.user?.id)
            this.instructorId = user?.faculty?.id

            params['instructor_id[eq]'] = this.instructorId

            this.courses = await courseApi.list(1, 1000, params)

            this.sections = (this.courses || []).flatMap((course: any) => {
                const sects = course.sections ?? [];
                const arr = Array.isArray(sects) ? sects : [sects];
                return arr
                    .filter((s: any) => s)
                    .map((s: any) => ({
                        ...s,
                        course_code: course.course_code,
                        name: course.name,
                        course_term: course.term,
                        course_year: course.year,
                        course_section_number: course.section_number,
                        course_room_number: course.room_number
                    }));
            }).filter((s: any) => s.instructor_id === this.instructorId);
            this.loading = false;
        }
    }
})
</script>
<template>
    <div class="p-4 w-full">
        <DataTable
            :value="sections"
            dataKey="id"
            :loading="loading"
            class="w-full"
            tableStyle="table-layout: auto"
        >
            <template #header>
                <div class="flex flex-row gap-2 items-center justify-between">
                    <h4 class="m-0">My Courses</h4>
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
                field="section_number"
                header="Section Number"
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
                field="room_number"
                header="Room Number"
                header-class="whitespace-nowrap"
                body-class="whitespace-nowrap"
                sortable
            />
        </DataTable>
    </div>
</template>