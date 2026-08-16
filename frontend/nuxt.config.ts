import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  ssr: false,

  compatibilityDate: '2025-07-15',

  devtools: {
    enabled: true,
  },

  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api/v1', // default fallback
    },
  },

  css: [
    '~/assets/css/main.css',
  ],

  vite: {
    plugins: [
      tailwindcss(),
    ],
  },
})