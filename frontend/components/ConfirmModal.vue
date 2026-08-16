<script setup lang="ts">
defineProps<{
  show: boolean
  title?: string
  message?: string
  loading?: boolean
}>()

defineEmits(['close', 'confirm'])
</script>

<template>
  <Modal :show="show" :title="title || 'Delete Confirmation'" @close="$emit('close')">
    <div class="space-y-5">
      <div class="flex items-start gap-4">
        <span class="text-3xl bg-rose-50 p-2.5 rounded-xl text-rose-600">⚠️</span>
        <div>
          <h4 class="text-sm font-bold text-slate-800">Warning</h4>
          <p class="text-sm text-slate-500 mt-1 font-medium leading-relaxed">
            {{ message || 'Are you sure you want to delete this record? This action cannot be undone.' }}
          </p>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <button
          type="button"
          class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition"
          @click="$emit('close')"
        >
          Cancel
        </button>
        <button
          type="button"
          :disabled="loading"
          class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-sm font-bold text-white transition disabled:opacity-50 shadow-sm shadow-rose-100"
          @click="$emit('confirm')"
        >
          {{ loading ? 'Deleting...' : 'Confirm Delete' }}
        </button>
      </div>
    </div>
  </Modal>
</template>
