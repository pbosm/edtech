export default defineNuxtConfig({
  devtools: { enabled: false },
  typescript: { strict: true },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || '/api'
    }
  }
});
