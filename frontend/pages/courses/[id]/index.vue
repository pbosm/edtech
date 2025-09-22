<script setup lang="ts">
  import { useRoute, useRouter } from 'vue-router'
  import { useToast } from '../../../composables/global/useToast'
  import { useCoursesApi } from '../../../composables/providers/useCoursesApi'
  import { useConfirm } from '../../../composables/global/useConfirm'
  import type { CourseDetail } from '../../../types/course'

  definePageMeta({ name: 'course-view' })

  const route = useRoute()
  const router = useRouter()
  const { push } = useToast()
  const { confirm } = useConfirm()
  const { show, destroy } = useCoursesApi()

  const id = computed(() => Number(route.params.id))

  const loading = ref(false)
  const loaded  = ref(false)
  const error   = ref<string | null>(null)
  const course = ref<CourseDetail | null>(null)

  async function fetchCourse () {
    if (!Number.isFinite(id.value)) {
      error.value = 'ID inválido.'
      loaded.value = true
      return
    }

    loading.value = true
    error.value = null

    try {
      const res = await show(id.value).catch((e:any)=>({ error:{ message: e?.message || 'Erro ao carregar curso' }}))
      if (!res || (res as any).error) throw new Error((res as any)?.error?.message || 'Erro ao carregar curso')

      // payload novo: { data: { ... , students: [...] } }
      course.value = (res as any).data?.data ?? (res as any).data ?? null
      if (!course.value) throw new Error('Curso não encontrado.')
    } catch (e:any) {
      error.value = e?.message || 'Erro ao carregar curso'
      push(error.value, 'error')
    } finally {
      loading.value = false
      loaded.value = true
    }
  }

  function goBack () { router.push('/courses') }
  function goEdit () { router.push(`/courses/${id.value}/edit`) }

  async function removeCourse () {
    if (!course.value) return

    const ok = await confirm({
      title: 'Excluir curso',
      message: `Excluir o curso "${course.value.name}"? Essa ação não pode ser desfeita.`,
      confirmText: 'Excluir',
      cancelText: 'Cancelar',
      tone: 'danger',
    })
    if (!ok) return

    const res = await destroy(id.value).catch((e:any)=>({ error:{ message: e?.message }}))
    if ((res as any)?.error) {
      push((res as any).error?.message || 'Falha ao excluir', 'error')
      return
    }

    push('Curso excluído com sucesso', 'success')
    router.push('/courses')
  }

  onMounted(fetchCourse)
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Detalhes do Curso</h1>
        <p class="text-slate-600 text-sm mt-2">Informações gerais e alunos matriculados</p>
      </div>

      <div class="flex items-center gap-2">
        <button class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2" @click="goBack">
          Voltar
        </button>
        <button class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2" @click="goEdit">
          Editar
        </button>
        <button class="rounded-xl bg-rose-600 hover:bg-rose-700 text-white px-4 py-2" @click="removeCourse">
          Excluir
        </button>
      </div>
    </div>

    <!-- Erro -->
    <div v-if="error" class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 p-4">
      {{ error }}
    </div>

    <!-- Conteúdo -->
    <div v-else>
      <!-- Card do curso -->
      <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-4 md:p-6 space-y-4">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">
              {{ course?.name || '—' }}
            </h2>
            <p class="text-slate-600 text-sm mt-1">
              {{ course?.description || 'Sem descrição.' }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
          <div class="rounded-lg border border-slate-200 bg-white p-3">
            <div class="text-xs text-slate-500">Duração</div>
            <div class="text-slate-900 font-medium">{{ course?.duration_hours ?? 0 }}h</div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-white p-3">
            <div class="text-xs text-slate-500">Criado em</div>
            <div class="text-slate-900 font-medium">
              {{ course?.created_at || '—' }}
            </div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-white p-3">
            <div class="text-xs text-slate-500">Atualizado em</div>
            <div class="text-slate-900 font-medium">
              {{ course?.updated_at || '—' }}
            </div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-white p-3">
            <div class="text-xs text-slate-500">Alunos</div>
            <div class="text-slate-900 font-medium">
              {{ course?.students_count ?? (course?.students?.length ?? 0) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Alunos -->
      <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-4 md:p-6 space-y-3 mt-5">
        <div class="flex items-center justify-between">
          <h3 class="text-base font-semibold text-slate-900">Alunos matriculados</h3>
          <div class="text-sm text-slate-600" v-if="course?.students?.length">
            Total: <b>{{ course.students.length }}</b>
          </div>
        </div>

        <div v-if="!course?.students?.length" class="text-slate-500 text-sm">
          Nenhum aluno matriculado.
        </div>

        <div v-else class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
          <table class="min-w-full">
            <thead>
            <tr class="text-left text-slate-500 text-xs">
              <th class="px-3 py-3">Aluno</th>
              <th class="px-3 py-3">E-mail</th>
              <th class="px-3 py-3">Cpf</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="s in course.students" :key="s.id" class="border-t border-slate-100">
              <td class="px-3 py-3">
                <div class="font-medium text-slate-900">{{ s.name || '—' }}</div>
              </td>
              <td class="px-3 py-3 text-slate-700">{{ s.email || '—' }}</td>
              <td class="px-3 py-3 text-slate-700">
                {{ s.cpf }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
