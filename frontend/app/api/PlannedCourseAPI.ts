import {BaseApi} from "~/api/BaseAPI";

export interface PlannedCourse {
    term: string
    year: number
}

export class PlannedCourseApi extends BaseApi<PlannedCourse, never> {
    constructor() {
        super('planned_course_pivots')
    }
}

export const plannedCourseApi = new PlannedCourseApi()
