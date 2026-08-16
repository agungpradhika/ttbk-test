<script setup lang="ts">
import { computed, ref, watch } from 'vue'

const route = useRoute()

// Mengambil title dari meta halaman secara dinamis (default: 'TBK')
const pageTitle = computed(() => (route.meta.title as string) || 'TBK')

// State kontrol visibilitas sidebar pada layar mobile
const isSidebarOpen = ref(false)

// Otomatis tutup sidebar mobile saat pindah rute/halaman
watch(() => route.path, () => {
  isSidebarOpen.value = false
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex overflow-x-hidden">
    <!-- Sidebar Kiri (Dengan pendeteksi responsive) -->
    <AppSidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

    <!-- Konten Utama (Kanan) -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0 w-full transition-all duration-300">
      <!-- Header Atas (Dilengkapi tombol hamburger menu) -->
      <AppHeader :title="pageTitle" @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />

      <!-- Area Halaman Aktif -->
      <main class="flex-1 p-5 md:p-8 overflow-y-auto">
        <slot />
      </main>
    </div>

    <!-- Floating Toast Alert -->
    <ToastNotification />
  </div>
</template>
