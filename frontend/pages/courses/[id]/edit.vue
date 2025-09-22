<script setup lang="ts">
  import { useRoute, useRouter } from 'vue-router'
  import { useToast } from '../../../composables/global/useToast'
  import { useCoursesApi } from '../../../composables/providers/useCoursesApi'
  import {Course} from "../../../types/course";
  import { submitResponse, type FieldErrors } from '../../../helpers/submitResponse'

  definePageMeta({ name: 'course-edit' })

  const route = useRoute()
  const router = useRouter()
  const { push } = useToast()
  const { show, update } = useCoursesApi()

  const id = computed(() => Number(route.params.id))

  const submitting = ref(false)
  const loading = ref(false)
  const loaded = ref(false)
  const loadError = ref<string | null>(null)

  type Form = {
    name: string
    description: string
    duration_hours: number | null
  }

  const form = reactive<Form>({
    name: '',
    description: '',
    duration_hours: null,
  })

  const errors = reactive<FieldErrors<Form>>({
    name: null,
    description: null,
    duration_hours: null,
  })

  function validate (): boolean {
    errors.name = !form.name.trim() ? 'Informe o nome do curso.' : null

    if (form.duration_hours == null || Number.isNaN(form.duration_hours)) {
      errors.duration_hours = 'Informe a duração em horas.'
    } else if (form.duration_hours < 0) {
      errors.duration_hours = 'A duração não pode ser negativa.'
    } else if (!Number.isInteger(form.duration_hours)) {
      errors.duration_hours = 'Use um número inteiro (ex: 40).'
    } else {
      errors.duration_hours = null
    }

    errors.description = null
    return !errors.name && !errors.duration_hours
  }

  async function fetchCourse () {
    if (!Number.isFinite(id.value)) {
      loadError.value = 'ID inválido.'
      loaded.value = true
      return
    }

    loading.value = true
    loadError.value = null

    try {
      const res = await show(id.value).catch((e:any)=>({ error:{ message: e?.message || 'Erro ao carregar curso' }}))
      if (!res || (res as any).error) throw new Error((res as any)?.error?.message || 'Erro ao carregar curso')

      const c: Course = (res as any).data?.data ?? (res as any).data

      if (!c)
        throw new Error('Curso não encontrado.')

      form.name = c.name ?? ''
      form.description = (c.description as any) ?? ''
      form.duration_hours = Number.isFinite(c.duration_hours) ? (c.duration_hours as number) : null
    } catch (e:any) {
      loadError.value = e?.message || 'Erro ao carregar curso'
      push(loadError.value, 'error')
    } finally {
      loading.value = false
      loaded.value = true
    }
  }

  async function submit () {
    if (submitting.value)
      return

    if (!validate())
      return

    submitting.value = true
    try {
      const payload = {
        name: form.name.trim(),
        description: form.description?.trim() || null,
        duration_hours: form.duration_hours ?? 0,
      }

      const res = await update(id.value, payload)
      const { ok, data } = submitResponse<Form, any>(res, errors)

      if (!ok)
        return

      push(data?.message ?? 'Curso atualizado com sucesso!', 'success')
      router.push(`/courses/${id.value}`)
    } finally {
      submitting.value = false
    }
  }

  function cancel () {
    router.push(`/courses`)
  }

  onMounted(fetchCourse)
  watch(() => route.params.id, fetchCourse)
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Editar Curso</h1>
        <p class="text-slate-600 text-sm mt-2">Atualize as informações do curso</p>
      </div>

      <div class="flex items-center gap-2">
        <button
            class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2"
            :disabled="submitting"
            @click="cancel"
        >
          Cancelar
        </button>
        <button
            class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2"
            :disabled="submitting"
            @click="submit"
        >
          Salvar
        </button>
      </div>
    </div>

    <!-- Mensagem de erro de carga -->
    <div v-if="loadError" class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 p-4">
      {{ loadError }}
    </div>

    <!-- Form -->
    <div v-else class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-4 md:p-6">
      <form class="grid grid-cols-1 gap-4" @submit.prevent="submit">
        <!-- Nome -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nome do curso</label>
          <input
              v-model.trim="form.name"
              type="text"
              placeholder="Ex.: Programação Web com Vue"
              class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
              :class="errors.name ? 'border-rose-300' : ''"
              :disabled="loading"
          />
          <p v-if="errors.name" class="mt-1 text-xs text-rose-600">{{ errors.name }}</p>
        </div>

        <!-- Descrição -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Descrição (opcional)</label>
          <textarea
              v-model.trim="form.description"
              rows="4"
              placeholder="Uma breve descrição do conteúdo do curso…"
              class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
              :disabled="loading"
          />
          <p v-if="errors.description" class="mt-1 text-xs text-rose-600">{{ errors.description }}</p>
        </div>

        <!-- Duração -->
        <div class="max-w-xs">
          <label class="block text-sm font-medium text-slate-700 mb-1">Duração (horas)</label>
          <input
              v-model.number="form.duration_hours"
              type="number"
              min="0"
              step="1"
              placeholder="40"
              class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
              :class="errors.duration_hours ? 'border-rose-300' : ''"
              :disabled="loading"
          />
          <p v-if="errors.duration_hours" class="mt-1 text-xs text-rose-600">{{ errors.duration_hours }}</p>
        </div>
      </form>
    </div>
  </div>
</template>
