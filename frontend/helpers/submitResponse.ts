export type FieldErrors<T> = Record<keyof T, string | null>
type BackendErrors = Record<string, string[] | string | undefined>

function _flattenFirst(msg?: string[] | string | undefined): string | null {
    if (!msg) return null
    return Array.isArray(msg) ? (msg[0] ?? null) : String(msg)
}

export function clearFieldErrors<T extends object>(errors: FieldErrors<T>): void {
    (Object.keys(errors) as (keyof T)[]).forEach(k => (errors[k] = null))
}

export function applyFieldErrors<T extends object>(
    errorsState: FieldErrors<T>,
    beErrors?: BackendErrors
): void {
    if (!beErrors || typeof beErrors !== 'object') return
        ;(Object.keys(errorsState) as (keyof T)[]).forEach((k) => {
        errorsState[k] = _flattenFirst(beErrors[String(k)])
    })
}

export function submitResponse<TForm extends object, TResp = any>(
    res: { data?: TResp; error?: any },
    errorsState: FieldErrors<TForm>
): { ok: boolean; data?: TResp; error?: any } {
    clearFieldErrors(errorsState)

    if (res?.error) {
        const beErrors: BackendErrors = res.error?.errors || res?.data?.errors
        applyFieldErrors(errorsState, beErrors)
        return { ok: false, error: res.error }
    }

    return { ok: true, data: res?.data }
}
