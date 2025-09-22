export default defineNuxtConfig({
  devtools: { enabled: false },
  app: { head: { title: 'EdTech' } },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || '/api/v1',
    }
  },
  modules: ['@nuxtjs/tailwindcss'],
  css: ['@/assets/css/tailwind.css'],
  postcss: { plugins: { tailwindcss: {}, autoprefixer: {} } },
  typescript: { strict: true },
})
