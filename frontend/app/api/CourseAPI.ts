import {BaseApi} from "~/api/BaseAPI";

export type Course = {
    id: number
    name: string
}

export class CourseApi extends BaseApi<Course, {}> {
    constructor() {
        super('courses')
    }
}

export const courseApi = new CourseApi()
