<script setup lang="ts">
  import { useLoading } from '../composables/global/useLoading'

  import AppSider from '../components/layout/PublicSiderBard.vue'
  import AppToast from '../components/ui/AppToast.vue'
  import AppSpinner from '../components/ui/AppSpinner.vue'
  import AppFooter from '../components/layout/PublicFooter.vue'
  import AppHeader from "../components/layout/PublicHeader.vue";

  const { isLoading } = useLoading?.() || { isLoading: { value: false } }
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    <div class="pointer-events-none fixed inset-0 -z-10">
      <div class="absolute -top-24 left-1/2 h-72 w-[1200px] -translate-x-1/2 rounded-full bg-gradient-to-r from-indigo-200 via-sky-200 to-emerald-200 blur-3xl opacity-40" />
    </div>

    <AppHeader />

    <div class="flex flex-1 gap-0">
      <AppSider />
      <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
        <slot />
      </main>
    </div>

    <AppFooter />

    <AppToast />
    <AppSpinner v-if="isLoading" />
  </div>
</template>
