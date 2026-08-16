<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Category, CategoryType } from '~/types/category'

const props = defineProps<{
  category?: Category | null // null = tambah baru, object = edit
  errors?: Record<string, string[]>
  loading?: boolean
}>()

const emit = defineEmits(['submit', 'cancel'])

// State form lokal
const name = ref('')
const type = ref<CategoryType>('expense')

// Sinkronisasi data jika masuk mode edit (props category terisi)
watch(
  () => props.category,
  (newVal) => {
    if (newVal) {
      name.value = newVal.name
      type.value = newVal.type
    } else {
      name.value = ''
      type.value = 'expense'
    }
  },
  { immediate: true }
)

const handleSubmit = () => {
  emit('submit', {
    name: name.value,
    type: type.value
  })
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <!-- Category Name -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Category Name</label>
      <input
        v-model="name"
        type="text"
        placeholder="e.g. Salary, Meals, Transport"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
        required
      />
      <span v-if="errors?.name" class="text-xs text-rose-500 mt-1 block">
        {{ errors.name[0] }}
      </span>
    </div>

    <!-- Category Type -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Type</label>
      <select
        v-model="type"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 bg-white"
        required
      >
        <option value="income">Income</option>
        <option value="expense">Expense</option>
      </select>
      <span v-if="errors?.type" class="text-xs text-rose-500 mt-1 block">
        {{ errors.type[0] }}
      </span>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
      <button
        type="button"
        class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition"
        @click="$emit('cancel')"
      >
        Cancel
      </button>
      <button
        type="submit"
        :disabled="loading"
        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-sm font-semibold text-white transition disabled:opacity-50"
      >
        {{ loading ? 'Saving...' : 'Save Category' }}
      </button>
    </div>
  </form>
</template>
