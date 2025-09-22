<script setup lang="ts">
  import { useRouter } from 'vue-router'
  import { useToast } from '../../composables/global/useToast'
  import { useCoursesApi } from '../../composables/providers/useCoursesApi'
  import {FieldErrors, submitResponse} from "../../helpers/submitResponse";
  import {Course} from "../../types/course";

  definePageMeta({ name: 'course-create' })

  type Form = {
    name: string
    description: string
    duration_hours: number | null
  }

  const router = useRouter()
  const { push } = useToast()
  const { create } = useCoursesApi()

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

  const submitting = ref(false)

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

  async function submit () {
    if (submitting.value)
      return

    if (!validate())
      return

    submitting.value = true
    try {
      const payload: Omit<Course, 'id' | 'created_at' | 'updated_at'> = {
        name: form.name.trim(),
        description: form.description?.trim() || null,
        duration_hours: form.duration_hours ?? 0,
      }

      const res = await create(payload)
      const { ok, data } = submitResponse<Form, any>(res, errors)

      if (!ok)
        return

      const created = data?.data ?? data
      push(data?.message ?? 'Curso criado com sucesso!', 'success')

      if (created?.id)
        router.push(`/courses/${created.id}`)
      else
        router.push('/courses')
    } finally {
      submitting.value = false
    }
  }

  function cancel () {
    router.push('/courses')
  }
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Novo Curso</h1>
        <p class="text-slate-600 text-sm mt-2">Preencha os dados para cadastrar um novo curso</p>
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

    <!-- Form -->
    <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-4 md:p-6">
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
          />
          <p v-if="errors.duration_hours" class="mt-1 text-xs text-rose-600">{{ errors.duration_hours }}</p>
        </div>
      </form>
    </div>
  </div>
</template>