<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import type { ChartOfAccount } from '~/types/chart-of-account'

const props = defineProps<{
  coa?: ChartOfAccount | null // null = tambah, object = edit
  errors?: Record<string, string[]>
  loading?: boolean
}>()

const emit = defineEmits(['submit', 'cancel'])

// Menggunakan composable Kategori untuk mengisi pilihan dropdown
const { categories, fetchCategories } = useCategories()

// State form lokal
const code = ref('')
const name = ref('')
const categoryId = ref<number | ''>('')

onMounted(() => {
  fetchCategories()
})

// Sinkronisasi data ketika mode edit aktif
watch(
  () => props.coa,
  (newVal) => {
    if (newVal) {
      code.value = newVal.code
      name.value = newVal.name
      categoryId.value = newVal.category_id
    } else {
      code.value = ''
      name.value = ''
      categoryId.value = ''
    }
  },
  { immediate: true }
)

const handleSubmit = () => {
  emit('submit', {
    code: code.value,
    name: name.value,
    category_id: Number(categoryId.value)
  })
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <!-- Account Code -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Account Code</label>
      <input
        v-model="code"
        type="text"
        placeholder="e.g. 1101, 4001, 5001"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
        required
      />
      <span v-if="errors?.code" class="text-xs text-rose-500 mt-1 block">
        {{ errors.code[0] }}
      </span>
    </div>

    <!-- Account Name -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Account Name</label>
      <input
        v-model="name"
        type="text"
        placeholder="e.g. Main Cash, Service Revenue, Electricity Cost"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
        required
      />
      <span v-if="errors?.name" class="text-xs text-rose-500 mt-1 block">
        {{ errors.name[0] }}
      </span>
    </div>

    <!-- Account Category -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Account Category</label>
      <select
        v-model="categoryId"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none bg-white"
        required
      >
        <option value="" disabled>-- Select Category --</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.name }} ({{ cat.type === 'income' ? 'Income' : 'Expense' }})
        </option>
      </select>
      <span v-if="errors?.category_id" class="text-xs text-rose-500 mt-1 block">
        {{ errors.category_id[0] }}
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
        {{ loading ? 'Saving...' : 'Save Account' }}
      </button>
    </div>
  </form>
</template>
