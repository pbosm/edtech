<script setup lang="ts">
  const route = useRoute()

  const collapsed = useState<boolean>('sider-collapsed', () => false)

  const links = [
    { to: '/', label: 'Dashboard', icon: 'M3 12h18M12 3v18' },
    { to: '/courses', label: 'Cursos', icon: 'M4 6h16M4 12h16M4 18h10' },
    { to: '/students', label: 'Alunos', icon: 'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8' },
  ]

  const isActive = (path: string) =>
      route.path === path || route.path.startsWith(path + '/')
</script>

<template>
  <aside
      class="hidden md:block sticky top-14 shrink-0 border-r border-slate-200 bg-white/80 backdrop-blur"
      :class="collapsed ? 'w-16' : 'w-64'"
  >
    <div class="flex h-full flex-col">
      <div class="flex items-center justify-between px-3 py-3 border-b border-slate-200">
        <span v-if="!collapsed" class="text-xs font-medium text-slate-500">Módulos</span>
        <button
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 hover:bg-white"
            @click="collapsed = !collapsed"
            title="Colapsar"
        >
          <svg v-if="!collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="none">
            <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none">
            <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>

      <nav class="flex-1 overflow-y-auto px-2 py-2">
        <NuxtLink
            v-for="l in links"
            :key="l.to"
            :to="l.to"
            class="group relative mb-1 flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-slate-100"
            :class="isActive(l.to) ? 'bg-slate-100 text-slate-900 ring-1 ring-slate-200' : ''"
        >
          <svg class="h-5 w-5 text-slate-500 group-hover:text-slate-700" viewBox="0 0 24 24" fill="none">
            <path :d="l.icon" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span v-if="!collapsed" class="truncate">{{ l.label }}</span>
          <span v-if="isActive(l.to)" class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-0.5 rounded bg-indigo-600" />
        </NuxtLink>
      </nav>
    </div>
  </aside>
</template>
