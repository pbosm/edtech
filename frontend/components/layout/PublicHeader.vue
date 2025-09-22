<script setup lang="ts">
  const route = useRoute()
  const isActive = (path: string) =>
      route.path === path || route.path.startsWith(path + '/')

  const links = [
    { to: '/', label: 'Dashboard' },
    { to: '/courses', label: 'Cursos' },
    { to: '/students', label: 'Alunos' },
  ]
</script>

<template>
  <header class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/70 backdrop-blur supports-[backdrop-filter]:bg-white/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="hidden md:flex h-14 items-center justify-center">
        <nav class="flex items-center gap-6 text-sm">
          <NuxtLink
              v-for="l in links"
              :key="l.to"
              :to="l.to"
              class="hover:text-indigo-600 transition-colors"
              :class="isActive(l.to) ? 'text-indigo-600 font-medium' : ''"
          >
            {{ l.label }}
          </NuxtLink>
        </nav>
      </div>

      <div class="md:hidden py-2">
        <div class="-mx-4 px-4 overflow-x-auto no-scrollbar">
          <nav class="w-max mx-auto flex gap-2">
            <NuxtLink
                v-for="l in links"
                :key="l.to"
                :to="l.to"
                class="whitespace-nowrap rounded-xl border px-3 py-1.5 text-sm transition"
                :class="isActive(l.to)
          ? 'border-indigo-300 bg-indigo-50 text-indigo-700'
          : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'"
            >
              {{ l.label }}
            </NuxtLink>
          </nav>
        </div>
      </div>
    </div>
  </header>
</template>
