import {BaseApi} from "~/api/BaseAPI";

export interface PlannedCourse {
    term: string
    year: number
    course_section_id: number
    plan_of_study_id: number
    course_id: number
    status: string
}

export class PlannedCourseApi extends BaseApi<PlannedCourse, never> {
    constructor() {
        super('planned_course_pivots')
    }
}

export const plannedCourseApi = new PlannedCourseApi()
