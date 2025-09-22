<script setup lang="ts">
  import { useRouter } from 'vue-router'
  import { useToast } from '../../composables/global/useToast'
  import { useStudentsApi } from '../../composables/providers/useStudentsApi'
  import { maskCpf } from '../../helpers/masks'
  import { submitResponse, type FieldErrors } from '../../helpers/submitResponse'

  definePageMeta({ name: 'student-create' })

  type Form = { name: string; email: string; cpf: string }

  const router = useRouter()

  const { push } = useToast()
  const { create } = useStudentsApi()

  const form = reactive<Form>({ name: '', email: '', cpf: '' })
  const submitting = ref(false)
  const errors = reactive<FieldErrors<Form>>({
    name: null, email: null, cpf: null,
  })

  const isValidEmail = (v: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)

  function validate(): boolean {
    errors.name  = !form.name.trim() ? 'Informe o nome.' : null
    errors.email = !form.email.trim() ? 'Informe o e-mail.' : (!isValidEmail(form.email.trim()) ? 'E-mail inválido.' : null)
    errors.cpf = form.cpf.length !== 14 ? 'CPF deve estar no formato XXX.XXX.XXX-XX.' : null

    return !errors.name && !errors.email && !errors.cpf
  }

  async function submit () {
    if (submitting.value)
      return

    if (!validate())
      return

    submitting.value = true
    try {
      const res = await create({
        name: form.name.trim(),
        email: form.email.trim(),
        cpf: form.cpf,
      })

      const { ok, data } = submitResponse<Form, any>(res, errors)

      if (!ok)
        return

      push(data?.message ?? 'Aluno criado com sucesso!', 'success')
      router.push('/students')
    } finally {
      submitting.value = false
    }
  }

  function cancel() { router.push('/students') }
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Novo Aluno</h1>
        <p class="text-slate-600 text-sm mt-2">Preencha os dados para cadastrar um novo aluno</p>
      </div>

      <div class="flex items-center gap-2">
        <button class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2"
                :disabled="submitting"
                @click="cancel">Cancelar</button>
        <button class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2"
                :disabled="submitting"
                @click="submit">Salvar</button>
      </div>
    </div>

    <!-- Form -->
    <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-4 md:p-6">
      <form class="grid grid-cols-1 gap-4" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
          <input v-model.trim="form.name" type="text" placeholder="Ex.: Maria Silva"
                 class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="errors.name ? 'border-rose-300' : ''" />
          <p v-if="errors.name" class="mt-1 text-xs text-rose-600">{{ errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
          <input v-model.trim="form.email" type="email" placeholder="email@exemplo.com"
                 class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="errors.email ? 'border-rose-300' : ''" />
          <p v-if="errors.email" class="mt-1 text-xs text-rose-600">{{ errors.email }}</p>
        </div>

        <div class="max-w-xs">
          <label class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
          <input v-model="form.cpf" type="text" inputmode="numeric" placeholder="000.000.000-00"
                 class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="errors.cpf ? 'border-rose-300' : ''"
                 @input="form.cpf = maskCpf(form.cpf || '')" />
          <p v-if="errors.cpf" class="mt-1 text-xs text-rose-600">{{ errors.cpf }}</p>
        </div>
      </form>
    </div>
  </div>
</template>
