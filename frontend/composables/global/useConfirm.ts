import { reactive } from 'vue'

export type ConfirmOptions = {
    title?: string
    message?: string
    confirmText?: string
    cancelText?: string
    tone?: 'default' | 'danger'
}

type InternalState = {
    open: boolean
    opts: ConfirmOptions
    _resolve?: (v: boolean) => void
}

const state = reactive<InternalState>({
    open: false,
    opts: {
        title: 'Confirmar',
        message: 'Tem certeza?',
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        tone: 'default',
    },
})

function confirm(opts: ConfirmOptions = {}) {
    state.opts = { ...state.opts, ...opts }
    state.open = true
    return new Promise<boolean>((resolve) => {
        state._resolve = resolve
    })
}

function accept() {
    state.open = false
    state._resolve?.(true)
    state._resolve = undefined
}

function cancel() {
    state.open = false
    state._resolve?.(false)
    state._resolve = undefined
}

export const useConfirm = () => ({
    state,
    confirm,
    accept,
    cancel,
})
