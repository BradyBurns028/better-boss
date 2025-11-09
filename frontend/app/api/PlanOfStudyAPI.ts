import {BaseApi, type FilterOperator} from "~/api/BaseAPI";

export interface PlanOfStudy {
    student_id: number
    degree_program_id: number
}

type PlanOfStudyFilters = {
    student_id?: FilterOperator<number>
    degree_program_id?: FilterOperator<number>
}

export class PlanOfStudyApi extends BaseApi<PlanOfStudy, PlanOfStudyFilters> {
    constructor() {
        super('plans_of_study')
    }
}

export const planOfStudyApi = new PlanOfStudyApi()
