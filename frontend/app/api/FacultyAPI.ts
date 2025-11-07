import {BaseApi} from "~/api/BaseAPI";

export type Faculty = {
    id: number
    name: string
}

export class FacultyApi extends BaseApi<Faculty, {}> {
    constructor() {
        super('faculties')
    }
}

export const facultyApi = new FacultyApi()
