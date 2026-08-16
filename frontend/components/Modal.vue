<script setup lang="ts">
defineProps<{
  show: boolean
  title: string
}>()

defineEmits(['close'])
</script>

<template>
  <Transition
    enter-active-class="ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-slate-500/75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

        <!-- Trik untuk memposisikan modal di tengah layar -->
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <!-- Panel Modal -->
        <div class="relative inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle border border-slate-100">
          <!-- Header Modal -->
          <div class="bg-slate-50 px-6 py-4 flex items-center justify-between border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" id="modal-title">
              {{ title }}
            </h3>
            <button 
              type="button" 
              class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
              @click="$emit('close')"
            >
              <span class="text-xl">✕</span>
            </button>
          </div>

          <!-- Isi Modal (Slot) -->
          <div class="bg-white px-6 py-6">
            <slot />
          </div>
        </div>

      </div>
    </div>
  </Transition>
</template>
