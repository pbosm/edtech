<script setup lang="ts">
  import { useToast } from "../../composables/global/useToast";
  const { toasts, remove } = useToast();
</script>

<template>
  <div class="fixed top-4 right-4 z-50 pointer-events-none">
    <transition-group
        name="toast"
        tag="div"
        class="w-80 space-y-2"
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
      <div
          v-for="t in toasts"
          :key="t.id"
          role="alert"
          aria-live="assertive"
          class="pointer-events-auto relative w-full rounded-md px-4 py-3 shadow-md text-white"
          :class="{
          'bg-green-600': t.type === 'success',
          'bg-red-600': t.type === 'error',
          'bg-blue-600': t.type === 'info',
          'bg-amber-600': t.type === 'warning',
        }"
      >
        <span class="text-sm break-words block pr-8">
          {{ t.message }}
        </span>

        <button
            class="absolute top-2 right-2 inline-flex h-6 w-6 items-center justify-center rounded text-white/80 hover:text-white hover:bg-white/20"
            @click="remove(t.id)"
            aria-label="Fechar"
        >
          <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>
