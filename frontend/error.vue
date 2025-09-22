<script setup lang="ts">
  const props = defineProps<{ error: { statusCode?: number; message?: string } }>()
  const router = useRouter()

  const is404 = computed(() => (props.error?.statusCode ?? 500) === 404)

  function goHome() {
    clearError({ redirect: '/' })
  }

  function goBack() {
    if (process.client && window.history.length > 1) {
      router.back()
    } else {
      goHome()
    }
  }
</script>

<template>
  <div class="min-h-[70vh] grid place-items-center p-6">
    <div class="max-w-xl w-full rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-8 text-center shadow-sm">
      <div class="flex items-center justify-center gap-3 text-slate-900">
        <div class="grid h-12 w-12 place-items-center rounded-xl"
             :class="is404 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600'">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
            <path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="text-left">
          <h1 class="text-2xl font-semibold">
            {{ is404 ? 'Página não encontrada' : 'Ocorreu um erro' }}
          </h1>
          <p class="text-slate-600">
            {{ is404 ? 'A rota que você tentou acessar não existe.'
              : (error?.message || 'Tente novamente mais tarde.') }}
          </p>
        </div>
      </div>

      <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
        <button @click="goBack"
                class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2">
          Voltar
        </button>
        <button @click="goHome"
                class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2">
          Ir para a Home
        </button>
      </div>

      <p v-if="!is404 && error?.statusCode"
         class="mt-4 text-xs text-slate-500">
        Código do erro: {{ error.statusCode }}
      </p>
    </div>
  </div>
</template>
