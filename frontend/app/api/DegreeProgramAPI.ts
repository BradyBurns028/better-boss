import {BaseApi} from "~/api/BaseAPI";

export type DegreeProgramAPI = {
    id: number
    name: string
}

export class DegreeProgramApi extends BaseApi<DegreeProgramAPI, {}> {
    constructor() {
        super('degree_programs')
    }
}

export const degreeProgramApi = new DegreeProgramApi()