<script setup lang="ts">
  import { useRouter } from 'vue-router'
  import { useToast } from '../../composables/global/useToast'
  import { useStudentsApi } from '../../composables/providers/useStudentsApi'
  import type { Student } from '../../types/student'
  import AppPagination from '../../components/ui/AppPagination.vue'
  import AppTable, { type AppTableColumn } from '../../components/ui/AppTable.vue'

  definePageMeta({ name: 'students' })

  const columns: AppTableColumn<Student>[] = [
    { key: 'name',       label: 'Aluno',    width: '40%' },
    { key: 'email',      label: 'Email' },
    { key: 'cpf',        label: 'CPF',      align: 'left' },
    { key: 'created_at', label: 'Criado em', align: 'left' },
  ]

  const router = useRouter()
  const { push } = useToast()
  const { list } = useStudentsApi()

  const filterMsg = ref('')
  const page = ref(1)
  const perPage = ref(12)

  const loading = ref(false)
  const loaded  = ref(false)
  const error   = ref<string|null>(null)

  const meta = reactive<{ total:number; last_page:number }>({ total: 0, last_page: 1 })
  const students = ref<Student[]>([])

  let requestToken = 0

  async function fetchStudents () {
    const myToken = ++requestToken
    loading.value = true
    error.value = null

    try {
      const res = await list(page.value, perPage.value, filterMsg.value.trim())
          .catch((e:any)=>({ error:{ message: e?.message || 'Erro inesperado' }}))

      if (myToken !== requestToken)
        return

      if (!res || (res as any).error)
        throw new Error((res as any)?.error?.message || 'Erro ao carregar alunos')

      const listData = (res as any).data?.data ?? (res as any).data ?? []
      students.value = listData

      const total = (res as any).data?.meta?.total ?? (res as any).data?.total ?? listData.length
      meta.total = Number.isFinite(total) ? total : listData.length

      const lastPage =
          (res as any).data?.meta?.last_page ??
          Math.max(1, Math.ceil(meta.total / perPage.value))
      meta.last_page = lastPage
    } catch (e:any) {
      error.value = e?.message || 'Erro ao carregar alunos'
      push(error.value, 'error')
    } finally {
      if (myToken === requestToken) {
        loading.value = false
        loaded.value  = true
      }
    }
  }

  function resetAndSearch () {
    page.value = 1
    fetchStudents()
  }

  function goCreate () { router.push('/students/create') }

  // primeira carga
  onMounted(fetchStudents)

  // observa apenas paginação (busca só ao clicar/enter)
  watch([page, perPage], fetchStudents)
</script>

<template>
  <div class="space-y-6">
    <!-- Header / ação -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Alunos</h1>
        <p class="text-slate-600 text-sm mt-2">Listagem de alunos cadastrados</p>
      </div>

      <button class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2"
              @click="goCreate">
        + Novo aluno
      </button>
    </div>

    <!-- Filtros -->
    <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-3">
      <div class="flex flex-wrap items-center gap-3">
        <input
            v-model.trim="filterMsg"
            @keyup.enter="resetAndSearch"
            type="text"
            placeholder="Buscar por nome ou e-mail…"
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

    <!-- TABELA -->
    <div v-else class="space-y-3">
      <AppTable
          :items="students"
          :columns="columns"
          :loading="loading"
          :error="null"
          empty-text="Nenhum aluno encontrado."
          :hide-actions="true"
      >
        <template #cell-name="{ row }">
          <div class="font-medium text-slate-900">{{ row.name }}</div>
          <div class="text-slate-600 text-xs">{{ row.email }}</div>
        </template>

        <template #cell-cpf="{ value }">
          <span class="text-slate-700">{{ value || '—' }}</span>
        </template>

        <template #cell-created_at="{ value }">
          <span class="text-slate-700">{{ value }}</span>
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
