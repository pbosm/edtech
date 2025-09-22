<script setup lang="ts">
  const props = withDefaults(defineProps<{
    modelValue: number
    last: number
    total?: number
    disabled?: boolean
  }>(), {
    total: 0,
    disabled: false,
  })

  const emit = defineEmits<{
    (e: 'update:modelValue', v: number): void
  }>()

  const canPrev = computed(() => !props.disabled && props.modelValue > 1)
  const canNext = computed(() => !props.disabled && props.modelValue < Math.max(1, props.last))

  function prev() {
    if (!canPrev.value) return
    emit('update:modelValue', Math.max(1, props.modelValue - 1))
  }

  function next() {
    if (!canNext.value) return
    emit('update:modelValue', Math.min(props.last || 1, props.modelValue + 1))
  }
</script>

<template>
  <div class="flex items-center justify-between gap-3 pt-4">
    <div class="text-sm text-slate-600">
      Total: <b>{{ total }}</b>
    </div>
    <div class="flex items-center gap-2">
      <button
          class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 disabled:opacity-50"
          :disabled="!canPrev"
          @click="prev"
      >&lt; Anterior</button>

      <span class="text-sm text-slate-700">
        Página {{ Math.min(modelValue, Math.max(1, last)) }} de {{ Math.max(1, last) }}
      </span>

      <button
          class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 disabled:opacity-50"
          :disabled="!canNext"
          @click="next"
      >Próxima &gt;</button>
    </div>
  </div>
</template>
