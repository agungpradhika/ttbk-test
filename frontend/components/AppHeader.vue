<script setup lang="ts">
defineProps<{
  title: string
}>()

defineEmits(['toggle-sidebar'])
</script>

<template>
  <header class="border-b bg-white px-6 md:px-8 py-5 flex items-center justify-between sticky top-0 z-20 no-print">
    <div class="flex items-center gap-3">
      <!-- Hamburger Button (Only visible on mobile lg:hidden) -->
      <button 
        @click="$emit('toggle-sidebar')" 
        class="lg:hidden p-1.5 rounded-xl hover:bg-slate-100 transition text-slate-600 focus:outline-none"
        aria-label="Toggle Sidebar"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <h2 class="text-xl font-bold text-slate-800">
        {{ title }}
      </h2>
    </div>
  </header>
</template>
