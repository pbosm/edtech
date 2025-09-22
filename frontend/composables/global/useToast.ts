type ToastType = 'success' | 'error' | 'info' | 'warning'
type Toast = { id: number; message: string; type: ToastType; timeout: number }

export function useToast() {
    const toasts = useState<Toast[]>('toasts', () => [])

    const remove = (id: number) => {
        const i = toasts.value.findIndex(t => t.id === id)
        if (i !== -1) toasts.value.splice(i, 1)
    }

    const clear = () => {
        toasts.value = []
    }

    const push = (message: string, type: ToastType = 'info', timeout = 3000) => {
        const id = Date.now() + Math.random()

        toasts.value.push({ id, message, type, timeout })

        if (process.client && timeout > 0) {
            setTimeout(() => remove(id), timeout)
        }
    }

    return { toasts, push, remove, clear }
}
