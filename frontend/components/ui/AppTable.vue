<script setup lang="ts">
  export type AppTableColumn<T=any> = {
    key: keyof T | string
    label: string
    width?: string
    align?: 'left'|'center'|'right'
    mobileHide?: boolean
    mobileLabel?: string
  }

  const props = withDefaults(defineProps<{
    items: any[]
    columns: AppTableColumn[]
    loading?: boolean
    error?: string | null
    emptyText?: string
    hideActions?: boolean
    stackOnMobile?: boolean
  }>(), {
    items: () => [],
    columns: () => [],
    loading: false,
    error: null,
    emptyText: 'Sem dados',
    hideActions: false,
    stackOnMobile: true,
  })
</script>

<template>
  <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-2">
    <!-- DESKTOP / TABLE -->
    <div class="overflow-x-auto hidden sm:block">
      <table class="min-w-full">
        <thead>
        <tr class="text-left text-slate-500 text-xs">
          <th v-for="c in columns" :key="String(c.key)" class="px-3 py-3" :style="{width: c.width}">
            {{ c.label }}
          </th>
          <th v-if="!hideActions && $slots.actions" class="px-3 py-3 text-right">Ações</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in items" :key="row.id ?? JSON.stringify(row)" class="border-t border-slate-100">
          <td v-for="c in columns" :key="String(c.key)" class="px-3 py-3"
              :class="{'text-right': c.align==='right','text-center': c.align==='center'}">
            <slot :name="`cell-${String(c.key)}`" :row="row" :value="row[c.key as any]">
              {{ row[c.key as any] ?? '—' }}
            </slot>
          </td>
          <td v-if="!hideActions && $slots.actions" class="px-3 py-3 text-right space-x-2">
            <slot name="actions" :row="row" />
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- MOBILE / STACK -->
    <div v-if="stackOnMobile" class="sm:hidden">
      <div v-if="items.length===0 && !loading" class="p-6 text-center text-slate-500">
        {{ emptyText }}
      </div>

      <ul v-else class="divide-y divide-slate-100">
        <li v-for="row in items" :key="row.id ?? JSON.stringify(row)" class="py-3 px-2">
          <!-- primeira linha: usa a primeira coluna como título -->
          <div class="text-sm">
            <slot :name="`cell-${String(columns[0]?.key)}`" :row="row" :value="row[columns[0]?.key as any]">
              <div class="font-medium text-slate-900">
                {{ row[columns[0]?.key as any] ?? '—' }}
              </div>
            </slot>
          </div>

          <!-- demais campos (exceto os com mobileHide) em 2 colunas -->
          <div class="mt-1 grid grid-cols-2 gap-x-3 gap-y-1 text-xs">
            <template v-for="c in columns.slice(1)" :key="String(c.key)">
              <template v-if="!c.mobileHide">
                <div class="text-slate-500">{{ c.mobileLabel || c.label }}</div>
                <div class="text-slate-800 text-right">
                  <slot :name="`cell-${String(c.key)}`" :row="row" :value="row[c.key as any]">
                    {{ row[c.key as any] ?? '—' }}
                  </slot>
                </div>
              </template>
            </template>
          </div>

          <!-- ações (se houver) -->
          <div v-if="!hideActions && $slots.actions" class="mt-2 flex justify-end space-x-2">
            <slot name="actions" :row="row" />
          </div>
        </li>
      </ul>
    </div>

    <!-- vazio (desktop) -->
    <div v-if="!loading && items.length===0" class="hidden sm:block p-6 text-center text-slate-500">
      {{ emptyText }}
    </div>
  </div>
</template>
