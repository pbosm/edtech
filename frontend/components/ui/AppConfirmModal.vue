<script setup lang="ts">
  import { onMounted, onBeforeUnmount } from 'vue'
  import { useConfirm } from '../../composables/global/useConfirm'

  const { state, accept, cancel } = useConfirm()

  function onKey(e: KeyboardEvent) {
    if (!state.open)
      return

    if (e.key === 'Escape') cancel()
    if (e.key === 'Enter') accept()
  }

  onMounted(() => window.addEventListener('keydown', onKey))
  onBeforeUnmount(() => window.removeEventListener('keydown', onKey))
</script>

<template>
  <teleport to="body">
    <div
        v-if="state.open"
        class="fixed inset-0 z-[1000] flex items-center justify-center"
        aria-modal="true" role="dialog"
    >
      <div class="absolute inset-0 bg-slate-900/50" @click="cancel" />

      <div
          class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-5 shadow-xl"
      >
        <h3 class="text-lg font-semibold text-slate-900">
          {{ state.opts.title || 'Confirmar' }}
        </h3>

        <p class="mt-2 text-sm text-slate-600">
          {{ state.opts.message || 'Tem certeza?' }}
        </p>

        <div class="mt-5 flex items-center justify-end gap-2">
          <button
              class="px-3 py-2 rounded-lg text-sm bg-slate-100 text-slate-700 hover:bg-slate-200"
              @click="cancel"
          >
            {{ state.opts.cancelText || 'Cancelar' }}
          </button>

          <button
              class="px-3 py-2 rounded-lg text-sm text-white"
              :class="state.opts.tone === 'danger'
              ? 'bg-rose-600 hover:bg-rose-700'
              : 'bg-indigo-600 hover:bg-indigo-700'"
              @click="accept"
          >
            {{ state.opts.confirmText || 'Confirmar' }}
          </button>
        </div>
      </div>
    </div>
  </teleport>
</template>
