<script setup lang="ts">
// Mengakses state toast global
const { message, type, show } = useToast()
</script>

<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show && message"
      class="fixed bottom-5 right-5 z-50 flex max-w-sm items-center rounded-xl p-4 shadow-lg border text-sm"
      :class="{
        'bg-emerald-50 text-emerald-800 border-emerald-200': type === 'success',
        'bg-rose-50 text-rose-800 border-rose-200': type === 'danger'
      }"
    >
      <!-- Ikon Sukses -->
      <span v-if="type === 'success'" class="mr-3 text-lg">🎉</span>
      <!-- Ikon Gagal/Bahaya -->
      <span v-else class="mr-3 text-lg">⚠️</span>
      
      <span class="font-medium">{{ message }}</span>
    </div>
  </Transition>
</template>
