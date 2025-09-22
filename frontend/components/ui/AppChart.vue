<script setup lang="ts">
  const props = withDefaults(defineProps<{
    labels: string[]
    values: number[]
    topN?: number
  }>(), {
    topN: 10,
  })

  const raw = computed(() => props.labels.map((l, i) => ({
    label: l ?? '',
    value: Number(props.values?.[i] ?? 0),
  })))

  const data = computed(() => {
    const arr = [...raw.value].sort((a, b) => b.value - a.value).slice(0, props.topN)
    return arr.map(d => ({
      ...d,
      short: d.label.length > 18 ? (d.label.slice(0, 18) + '…') : d.label
    }))
  })

  const maxValue = computed(() => {
    const m = Math.max(0, ...data.value.map(d => d.value))
    return m || 1
  })

  function niceStep(max: number) {
    const target = max / 4
    const pow10 = Math.pow(10, Math.floor(Math.log10(target || 1)))
    const candidates = [1, 2, 5].map(c => c * pow10)
    let step = candidates[0]
    for (const c of candidates) if (Math.abs(c - target) < Math.abs(step - target)) step = c
    return step || 1
  }
  const yTicks = computed(() => {
    const step = niceStep(maxValue.value)
    const ticks: number[] = []
    for (let v = 0; v <= maxValue.value + 0.5 * step; v += step) ticks.push(Math.round(v))
    return ticks
  })

  const padding = { top: 24, right: 16, bottom: 48, left: 40 }
  const width = 900
  const height = 320

  const plotW = width - padding.left - padding.right
  const plotH = height - padding.top - padding.bottom

  const barGap = 12
  const barW = computed(() => {
    const n = Math.max(1, data.value.length)
    return Math.max(12, (plotW - barGap * (n - 1)) / n)
  })

  function yScale(v: number) {
    const h = (v / maxValue.value) * plotH
    return Math.max(0, h)
  }

  const wrapper = ref<HTMLDivElement|null>(null)
  const hovering = ref<{ idx: number, x: number, y: number } | null>(null)

  function setTip(i: number, ev: MouseEvent) {
    if (!wrapper.value)
      return

    const wrap = wrapper.value.getBoundingClientRect()
    const target = ev.currentTarget as SVGRectElement
    const bar = target.getBoundingClientRect()

    hovering.value = {
      idx: i,
      x: bar.left - wrap.left + bar.width / 2,
      y: bar.top - wrap.top
    }
  }
  function showTip(i: number, ev: MouseEvent) { setTip(i, ev) }
  function moveTip(i: number, ev: MouseEvent) { setTip(i, ev) }
  function hideTip() { hovering.value = null }

  const allZero = computed(() => data.value.every(d => d.value === 0))
</script>

<template>
  <div ref="wrapper" class="relative w-full">
    <div
        v-if="allZero"
        class="flex h-48 items-center justify-center rounded-xl border border-slate-200 bg-white/60 text-sm text-slate-500"
    >
      Sem matrículas suficientes para visualizar o gráfico.
    </div>

    <div v-else class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-3">
      <svg
          :viewBox="`0 0 ${width} ${height}`"
          class="w-full h-56 sm:h-64 md:h-80 xl:h-[420px] block"
          preserveAspectRatio="none"
      >
        <g :transform="`translate(${padding.left},${padding.top})`">
          <g v-for="(t, i) in yTicks" :key="`y-${i}`">
            <line
                :x1="0" :x2="plotW"
                :y1="plotH - (t / maxValue) * plotH"
                :y2="plotH - (t / maxValue) * plotH"
                class="stroke-slate-200"
                stroke-width="1"
                stroke-dasharray="2 4"
            />
            <text
                :x="-8"
                :y="plotH - (t / maxValue) * plotH"
                text-anchor="end"
                dominant-baseline="middle"
                class="fill-slate-500 text-[10px]"
            >{{ t }}</text>
          </g>

          <g>
            <template v-for="(d, i) in data" :key="`b-${i}`">
              <defs>
                <linearGradient :id="`g-${i}`" x1="0" x2="0" y1="1" y2="0">
                  <stop offset="0%" stop-color="#E0EAFF"/>
                  <stop offset="100%" stop-color="#6366F1"/>
                </linearGradient>
              </defs>

              <rect
                  :x="i * (barW + barGap)"
                  :y="plotH - Math.max(3, yScale(d.value))"
                  :width="barW"
                  :height="Math.max(3, yScale(d.value))"
                  :fill="`url(#g-${i})`"
                  rx="8"
                  class="transition-all duration-500 ease-out cursor-pointer"
                  @mouseenter="showTip(i, $event)"
                  @mousemove="moveTip(i, $event)"
                  @mouseleave="hideTip"
              />

              <text
                  v-if="d.value > 0"
                  :x="i * (barW + barGap) + barW/2"
                  :y="plotH - Math.max(3, yScale(d.value)) - 6"
                  text-anchor="middle"
                  class="fill-slate-700 text-[11px] font-medium select-none"
              >{{ d.value }}</text>
            </template>
          </g>

          <g>
            <text
                v-for="(d, i) in data"
                :key="`l-${i}`"
                :x="i * (barW + barGap) + barW/2"
                :y="plotH + 14"
                text-anchor="end"
                transform-origin="center"
                :transform="`rotate(-35, ${i*(barW+barGap)+barW/2}, ${plotH+14})`"
                class="fill-slate-500 text-[10px] select-none"
            >{{ d.short }}</text>
          </g>
        </g>
      </svg>

      <!-- Tooltip -->
      <div
          v-if="hovering"
          class="pointer-events-none absolute -translate-x-1/2 -translate-y-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs shadow-md"
          :style="{
          left: `${hovering.x}px`,
          top: `${hovering.y}px`
        }"
      >
        <div class="font-medium text-slate-800">
          {{ data[hovering.idx].label }}
        </div>
        <div class="mt-0.5 text-slate-600">
          Alunos: <span class="font-semibold">{{ data[hovering.idx].value }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
