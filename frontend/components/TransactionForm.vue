<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'

const props = defineProps<{
  errors?: Record<string, string[]>
  loading?: boolean
}>()

const emit = defineEmits(['submit', 'cancel'])

const { coas, fetchCoas } = useChartOfAccounts()

// State form lokal
const transactionDate = ref(new Date().toISOString().split('T')[0])
const coaId = ref<number | ''>('')
const description = ref('')
const debit = ref<number>(0)
const credit = ref<number>(0)

onMounted(() => {
  fetchCoas()
})

// Mencari detail akun COA yang sedang dipilih untuk mendeteksi Tipenya (Income/Expense)
const selectedCoa = computed(() => {
  return coas.value.find(c => c.id === Number(coaId.value))
})

// Deteksi otomatis tipe akun
const isIncome = computed(() => selectedCoa.value?.category?.type === 'income')
const isExpense = computed(() => selectedCoa.value?.category?.type === 'expense')

// Reset debit/kredit jika jenis akun berganti
watch(coaId, () => {
  debit.value = 0
  credit.value = 0
})

const handleSubmit = () => {
  emit('submit', {
    transaction_date: transactionDate.value,
    coa_id: Number(coaId.value),
    description: description.value,
    debit: isExpense.value ? Number(debit.value) : 0,
    credit: isIncome.value ? Number(credit.value) : 0
  })
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <!-- Select COA -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Select Account (COA)</label>
      <select
        v-model="coaId"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none bg-white"
        required
      >
        <option value="" disabled>-- Select Account --</option>
        <option v-for="c in coas" :key="c.id" :value="c.id">
          {{ c.code }} - {{ c.name }} ({{ c.category?.name }})
        </option>
      </select>
      <span v-if="errors?.coa_id" class="text-xs text-rose-500 mt-1 block">
        {{ errors.coa_id[0] }}
      </span>
    </div>

    <!-- Date -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Transaction Date</label>
      <input
        v-model="transactionDate"
        type="date"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
        required
      />
      <span v-if="errors?.transaction_date" class="text-xs text-rose-500 mt-1 block">
        {{ errors.transaction_date[0] }}
      </span>
    </div>

    <!-- Description -->
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
      <input
        v-model="description"
        type="text"
        placeholder="e.g. Monthly office rent payment"
        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
        required
      />
      <span v-if="errors?.description" class="text-xs text-rose-500 mt-1 block">
        {{ errors.description[0] }}
      </span>
    </div>

    <!-- Debit / Credit Fields -->
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Debit (Expense)</label>
        <input
          v-model="debit"
          type="number"
          min="0"
          :disabled="isIncome || !coaId"
          placeholder="0"
          class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none disabled:bg-slate-100 disabled:text-slate-400"
          required
        />
        <span v-if="errors?.debit" class="text-xs text-rose-500 mt-1 block">
          {{ errors.debit[0] }}
        </span>
        <span v-if="isIncome" class="text-[10px] text-amber-600 mt-1 block">
          *Income account does not use Debit.
        </span>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Credit (Income)</label>
        <input
          v-model="credit"
          type="number"
          min="0"
          :disabled="isExpense || !coaId"
          placeholder="0"
          class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none disabled:bg-slate-100 disabled:text-slate-400"
          required
        />
        <span v-if="errors?.credit" class="text-xs text-rose-500 mt-1 block">
          {{ errors.credit[0] }}
        </span>
        <span v-if="isExpense" class="text-[10px] text-amber-600 mt-1 block">
          *Expense account does not use Credit.
        </span>
      </div>
    </div>

    <!-- Global validation messages -->
    <span v-if="errors?.amount" class="text-xs text-rose-500 block font-semibold">
      {{ errors.amount[0] }}
    </span>

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
        {{ loading ? 'Saving...' : 'Record Transaction' }}
      </button>
    </div>
  </form>
</template>
