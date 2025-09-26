export interface PaginationMeta {
    current_page: number
    per_page: number
    total: number
    last_page: number
}

export interface ApiSuccessResponse<T> {
    success: true
    message: string
    data: T
    pagination?: PaginationMeta
}

export interface ApiErrorResponse {
    success: false
    message: string
    errors?: any[]
}

export type ApiResponse<T> = ApiSuccessResponse<T> | ApiErrorResponse

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
        try {
            return await $fetch(`/api/${endpoint}`, {
                method,
                body,
                params,
                headers: getHeaders(body instanceof FormData),
            })
        } catch (e: any) {
            console.error(`[API ${method} ${endpoint}]`, e)
            return {
                success: false,
                message: e?.message || 'Network error',
            }
        }
    },

    get: <T>(endpoint: string, params?: Record<string, any>) =>
        apiService.request<T>('GET', endpoint, undefined, params),

    post: <T>(endpoint: string, body: any, params?: Record<string, any>) =>
        apiService.request<T>('POST', endpoint, body, params),

    put: <T>(endpoint: string, body: any, params?: Record<string, any>) =>
        apiService.request<T>('PUT', endpoint, body, params),

    delete: <T>(endpoint: string, params?: Record<string, any>) =>
        apiService.request<T>('DELETE', endpoint, undefined, params),
}