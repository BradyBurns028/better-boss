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

    protected buildFilters(filters: TFilter): Record<string, any> {
        const result: Record<string, any> = {}
        Object.entries(filters).forEach(([field, operators]) => {
            Object.entries(operators || {}).forEach(([op, val]) => {
                result[`${field}[${op}]`] = val
            })
        })
        return result
    }

    /**
     * Recursively convert object keys to camelCase.
     */
    protected transformPayload(obj: any): any {
        if (Array.isArray(obj)) return obj.map(this.transformPayload.bind(this));
        if (obj && typeof obj === 'object') {
            const out: Record<string, any> = {};
            for (const [k, v] of Object.entries(obj)) {
                const camel = k.replace(/_([a-z])/g, (_, l) => l.toUpperCase()); // snake → camel
                out[camel] = this.transformPayload(v);
            }
            return out;
        }
        return obj;
    }


    protected notify(ok: boolean, title: string, detail: string) {
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
        const res = await promise
        if (!res.success) {
            if (opts?.error) this.notify(false, opts.error, res.message)
            return null
        }
        if (opts?.success) this.notify(true, opts.success, res.message)
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

    async create(data: Partial<TModel>, msg = 'Created') {
        const payload = this.transformPayload(data)
        return this.unwrap<TModel>(
            apiService.post(this.resource, payload),
            { success: `${msg} successfully`, error: `Failed to create ${msg}` }
        )
    }

    async update(id: number, data: Partial<TModel>, msg = 'Updated') {
        const payload = this.transformPayload(data)
        return this.unwrap<TModel>(
            apiServiceV2.put(`${this.resource}/${id}`, payload),
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