<script setup lang="ts">
import { computed } from 'vue'

const route = useRoute()

// Mengambil title dari meta halaman secara dinamis (default: 'TBK')
const pageTitle = computed(() => (route.meta.title as string) || 'TBK')
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Sidebar Kiri -->
    <AppSidebar />

    <!-- Konten Utama (Kanan) -->
    <div class="flex-1 flex flex-col min-h-screen">
      <!-- Header Atas -->
      <AppHeader :title="pageTitle" />

      <!-- Area Halaman Aktif -->
      <main class="flex-1 p-8">
        <slot />
      </main>
    </div>

    <!-- Floating Toast Alert -->
    <ToastNotification />
  </div>
</template>
