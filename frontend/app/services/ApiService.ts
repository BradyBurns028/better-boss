export interface PaginationMeta {
    current_page: number
    per_page: number
    total: number
    last_page: number
}

export type ApiResponse<T> = {
    success: boolean
    data: T
    meta: PaginationMeta
    info: {
        status_code: number
        error?: {
            code: string
            message: string
            details: object
        }
    }
}

const getAuthToken = (): string | null =>
    typeof window !== 'undefined' ? localStorage.getItem('authToken') : null


const getHeaders = (isFormData = false): HeadersInit => {
    const token = getAuthToken()
    return {
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
    }
}

export const apiService = {
    request: async <T>(
        method: 'GET' | 'POST' | 'PUT' | 'DELETE',
        endpoint: string,
        body?: any,
        params?: Record<string, any>
    ): Promise<ApiResponse<T>> => {
        // @ts-ignore
        return await $fetch(`/api/${endpoint}`, {
            method,
            body,
            params,
            headers: getHeaders(body instanceof FormData),
        })
    },

    get: <T>(endpoint: string, params?: Record<string, any>): Promise<ApiResponse<T>> =>
        apiService.request<T>('GET', endpoint, undefined, params),

    post: <T>(endpoint: string, body: any, params?: Record<string, any>): Promise<ApiResponse<T>> =>
        apiService.request<T>('POST', endpoint, body, params),

    put: <T>(endpoint: string, body: any, params?: Record<string, any>): Promise<ApiResponse<T>> =>
        apiService.request<T>('PUT', endpoint, body, params),

    delete: <T>(endpoint: string, params?: Record<string, any>): Promise<ApiResponse<T>> =>
        apiService.request<T>('DELETE', endpoint, undefined, params),
}