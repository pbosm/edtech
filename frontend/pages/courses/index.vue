<script setup lang="ts">
  import { useToast } from '../../composables/global/useToast'
  import { useCoursesApi } from '../../composables/providers/useCoursesApi'
  import { useConfirm } from '../../composables/global/useConfirm'
  import type { Course } from '../../types/course'

  import AppPagination from '../../components/ui/AppPagination.vue'
  import AppTable, { type AppTableColumn } from '../../components/ui/AppTable.vue'

  definePageMeta({ name: 'courses' })

  const columns: AppTableColumn<Course>[] = [
    { key: 'name', label: 'Curso', width: '40%' },
    { key: 'duration_hours', label: 'Duração', align: 'left' },
    { key: 'students_count', label: 'Alunos', align: 'left' },
    { key: 'created_at', label: 'Criado em', align: 'left' },
  ]

  const router = useRouter()
  const { push } = useToast()
  const { list, destroy } = useCoursesApi()
  const { confirm } = useConfirm()

  const filterMsg = ref('')
  const page = ref(1)
  const perPage = ref(12)

  const loading = ref(false)
  const loaded  = ref(false)
  const error   = ref<string|null>(null)

  const meta = reactive<{ total:number; last_page:number }>({ total: 0, last_page: 1 })
  const courses = ref<Course[]>([])

  let requestToken = 0

  async function fetchCourses() {
    const myToken = ++requestToken
    loading.value = true
    error.value = null

    try {
      const res = await list(page.value, perPage.value, filterMsg.value.trim()).catch((e:any)=>({
        error: { message: e?.message || 'Erro inesperado' }
      }))
      if (myToken !== requestToken) return
      if (!res || (res as any).error) throw new Error((res as any)?.error?.message || 'Erro ao carregar cursos')

      const payload = (res as any).data
      const listData: Course[] = payload?.data ?? []
      courses.value = listData

      const m = payload?.meta
      meta.total = Number.isFinite(m?.total) ? m.total : listData.length
      meta.last_page = Number.isFinite(m?.last_page) ? m.last_page : 1
    } catch (e:any) {
      error.value = e?.message || 'Erro ao carregar cursos'
      push(error.value, 'error')
    } finally {
      if (myToken === requestToken) {
        loading.value = false
        loaded.value = true
      }
    }
  }

  function resetAndSearch() {
    page.value = 1
    fetchCourses()
  }

  function goCreate() { router.push('/courses/create') }
  function goView(c: Course) { router.push(`/courses/${c.id}`) }
  function goEdit(c: Course) { router.push(`/courses/${c.id}/edit`) }

  async function removeCourse(c: Course) {
    const ok = await confirm({
      title: 'Excluir curso',
      message: `Excluir o curso "${c.name}"? Essa ação não pode ser desfeita.`,
      confirmText: 'Excluir',
      cancelText: 'Cancelar',
      tone: 'danger',
    })

    if (!ok)
      return

    const res = await destroy(c.id).catch((e:any)=>({ error:{ message: e?.message }}))
    if ((res as any)?.error)
      return push((res as any).error?.message || 'Falha ao excluir', 'error')

    push('Curso excluído com sucesso', 'success')
    fetchCourses()
  }

  onMounted(fetchCourses)

  watch(page, fetchCourses)
  watch(perPage, () => { page.value = 1; fetchCourses() })
</script>

<template>
  <div class="space-y-6">
    <!-- Header / ações -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Cursos</h1>
        <p class="text-slate-600 text-sm mt-2">Listagem de cursos cadastrados</p>
      </div>

      <button class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2" @click="goCreate">
        + Novo curso
      </button>
    </div>

    <!-- Filtros -->
    <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-3">
      <div class="flex flex-wrap items-center gap-3">
        <input
            v-model.trim="filterMsg"
            type="text"
            placeholder="Buscar por nome ou descrição…"
            class="w-full md:w-80 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
        />
        <select v-model.number="perPage" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
          <option :value="6">6 por página</option>
          <option :value="12">12 por página</option>
          <option :value="24">24 por página</option>
        </select>
        <button class="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" @click="resetAndSearch">Filtrar</button>
      </div>
    </div>

    <!-- ERRO -->
    <div v-if="error" class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 p-4">
      {{ error }}
    </div>

    <!-- TABELA-->
    <div v-else class="space-y-3">
      <AppTable
          :items="courses"
          :columns="columns"
          :loading="loading"
          :error="null"
          empty-text="Nenhum curso encontrado."
          @row:click="goView"
      >
        <template #cell-name="{ row }">
          <div class="font-medium text-slate-900">{{ row.name }}</div>
          <div class="text-slate-600 text-xs line-clamp-1">{{ row.description || '—' }}</div>
        </template>

        <template #cell-duration_hours="{ value }">
          <span class="text-slate-700">{{ value }}h</span>
        </template>

        <template #cell-students_count="{ value }">
          <span class="text-slate-700">{{ value ?? 0 }}</span>
        </template>

        <template #cell-created_at="{ value }">
          <span class="text-slate-700">{{ value || '—' }}</span>
        </template>

        <template #actions="{ row }">
          <button class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                  :disabled="loading"
                  @click.stop="goView(row)">Ver</button>
          <button class="px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs"
                  :disabled="loading"
                  @click.stop="goEdit(row)">Editar</button>
          <button class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs"
                  :disabled="loading"
                  @click.stop="removeCourse(row)">Excluir</button>
        </template>
      </AppTable>

      <AppPagination
          v-if="meta.last_page > 1"
          v-model="page"
          :last="meta.last_page"
          :total="meta.total"
          :disabled="loading"
      />
    </div>
  </div>
</template>
