import { useLoading } from './useLoading'
import { useToast } from './useToast'

type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'

type RequestOpts = {
    method?: HttpMethod
    body?: any
    query?: any
    toast?: boolean
    throwOnError?: boolean
}

type ApiResult<T> = {
    data: T | null
    error: any | null
    ok: boolean
    status?: number
}

function flattenErrors(errors?: Record<string, string[] | string | undefined>): string[] {
    if (!errors || typeof errors !== 'object')
        return []

    const out: string[] = []
    for (const v of Object.values(errors)) {
        if (!v) continue
        if (Array.isArray(v)) out.push(...v.filter(Boolean).map(String))
        else out.push(String(v))
    }
    return [...new Set(out)]
}

function extractError(err: any) {
    const payload = err?.data ?? err?.response?._data ?? err?.response?.data ?? err
    const status  = err?.statusCode ?? err?.status ?? err?.response?.status
    return { payload, status }
}

export function useApi() {
    const config  = useRuntimeConfig()
    const base    = config.public.apiBase
    const loading = useLoading()
    const { push } = useToast()

    async function request<T>(url: string, opts: RequestOpts = {}): Promise<ApiResult<T>> {
        const {
            method = 'GET',
            body,
            query,
            toast = true,
            throwOnError = false,
        } = opts

        loading.start()
        try {
            const data = await $fetch<T>(url, {
                baseURL: base,
                method,
                body,
                query,
                headers: { 'Content-Type': 'application/json' },
            })
            return { ok: true, data, error: null, status: 200 }
        } catch (err: any) {
            const { payload, status } = extractError(err)

            if (toast) {
                const top = payload?.message || err?.message || 'Ocorreu um erro. Tente novamente.'
                if (top) push(top, 'error')
                flattenErrors(payload?.errors).forEach(m => push(m, 'error'))
            }

            if (throwOnError) throw { ...payload, status }

            return { ok: false, data: null as any, error: payload, status }
        } finally {
            loading.stop()
        }
    }

    return {
        get:  <T>(url: string, query?: any, extra?: Omit<RequestOpts,'method'|'query'>) =>
            request<T>(url, { method: 'GET', query, ...(extra||{}) }),

        post: <T>(url: string, body?: any, extra?: Omit<RequestOpts,'method'|'body'>) =>
            request<T>(url, { method: 'POST', body, ...(extra||{}) }),

        put:  <T>(url: string, body?: any, extra?: Omit<RequestOpts,'method'|'body'>) =>
            request<T>(url, { method: 'PUT', body, ...(extra||{}) }),

        del:  <T>(url: string, extra?: Omit<RequestOpts,'method'>) =>
            request<T>(url, { method: 'DELETE', ...(extra||{}) }),
    }
}
