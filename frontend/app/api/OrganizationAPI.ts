import {BaseApi} from "~/api/BaseAPI";
import {apiService} from '~/services/ApiService'

export type Organization = {
    id: number
    name: string
    address?: string
}

export class OrganizationApi extends BaseApi<Organization, {}> {
    constructor() {
        super('organizations')
    }
}

export const organizationApi = new OrganizationApi()
