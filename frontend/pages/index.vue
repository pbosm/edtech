<script setup lang="ts">
  import AppChart from '../components/ui/AppChart.vue'
  import { useToast } from '../composables/global/useToast'
  import { useCoursesApi } from '../composables/providers/useCoursesApi'
  import {CoursesDashboard} from "../types/courseDashboard";

  const { push } = useToast()
  const { dashboard } = useCoursesApi()

  const totals = reactive({ courses: 0, students: 0, avg_duration_hours: 0 })
  const labels = ref<string[]>([])
  const values = ref<number[]>([])
  const avgDuration = ref<number>(0)

  async function loadDashboard() {
    const res = await dashboard()

    if ((res as any)?.error)
      throw new Error((res as any).error?.message || 'Erro ao carregar dashboard')

    const data = (res as any).data as CoursesDashboard

    avgDuration.value = Math.round((data?.totals?.avg_duration_hours ?? 0) * 10) / 10
    totals.courses = data?.totals?.courses ?? 0
    totals.students = data?.totals?.students ?? 0
    totals.avg_duration_hours = data?.totals?.avg_duration_hours ?? 0
    labels.value = data?.chart?.labels ?? []
    values.value  = data?.chart?.values ?? []
  }

  onMounted(async () => {
    try {
      await loadDashboard()
    } catch (e:any) {
      push(e?.message || 'Erro ao carregar dashboard', 'error')
      totals.courses = 0
      totals.students = 0
      totals.avg_duration_hours = 0
      labels.value = []
      values.value  = []
    }
  })
</script>

<template>
  <div class="space-y-6">
    <!-- Título + ações -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-sm text-slate-600">Resumo geral da plataforma</p>
      </div>
      <div class="flex gap-2">
        <NuxtLink to="/courses/create" class="inline-flex h-10 items-center gap-2 rounded-xl bg-indigo-600 px-4 text-white hover:bg-indigo-500 shadow-sm">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Novo curso
        </NuxtLink>
        <NuxtLink to="/students/create" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 hover:bg-white/80">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Novo aluno
        </NuxtLink>
      </div>
    </div>

    <!-- Cards -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div class="group rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-500">Total de Cursos</h2>
            <p class="mt-2 text-3xl font-semibold">{{ totals.courses }}</p>
          </div>
          <div class="grid h-12 w-12 place-items-center rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21V4.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
        </div>
      </div>

      <div class="group rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-500">Total de Alunos</h2>
            <p class="mt-2 text-3xl font-semibold">{{ totals.students }}</p>
          </div>
          <div class="grid h-12 w-12 place-items-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M20 21v-2a6 6 0 0 0-6-6M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-4a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
        </div>
      </div>

      <div class="group rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-500">Média de horas por curso</h2>
            <p class="mt-2 text-3xl font-semibold">
              {{ avgDuration }}h
            </p>
          </div>
          <div class="grid h-12 w-12 place-items-center rounded-xl bg-sky-50 text-sky-600 group-hover:bg-sky-100">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
              <path d="M12 8v4l3 3M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
        </div>
      </div>
    </section>

    <!-- Gráfico -->
    <section class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-5 shadow-sm">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Alunos por curso</h3>
        <div class="text-xs text-slate-500">Top {{ labels.length }} cursos</div>
      </div>
      <div class="mt-4">
        <AppChart :labels="labels" :values="values" />
      </div>
    </section>
  </div>
</template>
