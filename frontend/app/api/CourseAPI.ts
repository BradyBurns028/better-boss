import {BaseApi} from "~/api/BaseAPI";
import {apiService} from '~/services/ApiService'

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
