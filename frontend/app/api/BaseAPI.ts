// ~/api/BaseApi.ts
import { apiService, type ApiResponse } from '~/services/ApiService'

export type FilterOperator<T> = {
    eq?: T
    ne?: T
    gt?: T
    lt?: T
    gte?: T
    lte?: T
}

export type FilterParams<T> = {
    [K in keyof T]?: FilterOperator<T[K]>
}

export abstract class BaseApi<TModel, TFilter extends FilterParams<any> = {}> {
    protected resource: string

    constructor(resource: string) {
        this.resource = resource
    }

    protected buildFilters(filters: Record<string, any>): Record<string, any> {
        const result: Record<string, any> = {}

        for (const [field, operators] of Object.entries(filters || {})) {
            if (operators === undefined || operators === null) continue

            // Case 1: primitive -> default to eq
            if (typeof operators !== 'object' || Array.isArray(operators)) {
                const val = operators
                if (typeof val === 'string' && val.trim() === '') continue
                result[`${field}`] = val
                continue
            }

            // Case 2: operator object -> { like: 'foo', gte: 2025, ... }
            for (const [op, val] of Object.entries(operators)) {
                if (val === undefined || val === null) continue
                if (typeof val === 'string' && val.trim() === '') continue
                result[`${field}[${op}]`] = val
            }
        }

        return result
    }

    protected notify(ok: boolean, title: string, detail: string|undefined) {
        useNuxtApp().$toast.add({
            severity: ok ? 'success' : 'error',
            summary: title,
            detail,
            life: ok ? 3000 : 5000,
        })
    }

    protected async unwrap<T>(
        promise: Promise<ApiResponse<T>>,
        opts?: { success?: string; error?: string }
    ): Promise<T | null> {
        const res: ApiResponse<T> = await promise
        if (!res.success) {
            if (opts?.error) this.notify(false, opts.error, res.info.error?.message)
            return null
        }
        if (opts?.success) this.notify(true, opts.success, undefined)
        return res.data
    }

    async list(
        page = 1,
        per = 25,
        filters: TFilter = {} as TFilter,
        extraParams: Record<string, any> = {}
    ) {
        return this.unwrap<{ data: TModel[]; pagination: any }>(
            apiService.get(this.resource, {
                page,
                per_page: per,
                ...this.buildFilters(filters),
                ...extraParams,
            })
        )
    }

    async all() {
        return this.unwrap<TModel[]>(apiService.get(this.resource))
    }

    async find(id: number) {
        return this.unwrap<TModel>(apiService.get(`${this.resource}/${id}`))
    }

    async create(data: Partial<TModel>, msg: string|undefined) {
        return this.unwrap<TModel>(
            apiService.post(this.resource, data),
            msg ? { success: `${msg} successfully`, error: `Failed to create ${msg}` }
                : undefined
        )
    }

    async update(id: number, data: Partial<TModel>, msg = 'Updated') {
        return this.unwrap<TModel>(
            apiService.put(`${this.resource}/${id}`, data),
            { success: `${msg} successfully`, error: `Failed to update ${msg}` }
        )
    }

    async destroy(id: number, msg = 'Deleted') {
        return this.unwrap<null>(
            apiService.delete(`${this.resource}/${id}`),
            { success: `${msg} successfully`, error: `Failed to delete ${msg}` }
        )
    }
}