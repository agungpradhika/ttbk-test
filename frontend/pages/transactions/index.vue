<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

definePageMeta({
  title: 'Transactions'
})

const {
  transactions,
  loading,
  errors,
  pagination,
  fetchTransactions,
  createTransaction
} = useTransactions()

// Load daftar COA untuk pilihan dropdown filter
const { coas, fetchCoas } = useChartOfAccounts()

const showModal = ref(false)

// State filter pencarian & pengelompokan
const fromDate = ref('')
const toDate = ref('')
const coaId = ref<number | ''>('')
const search = ref('')
const currentPage = ref(1)
const perPage = ref(25)

const loadTransactions = () => {
  fetchTransactions({
    from: fromDate.value,
    to: toDate.value,
    coa_id: coaId.value,
    search: search.value,
    page: currentPage.value,
    per_page: perPage.value
  })
}

// Pantau perubahan filter untuk otomatis memanggil API Backend (reset page ke 1)
watch([fromDate, toDate, coaId, search, perPage], () => {
  currentPage.value = 1
  loadTransactions()
})

// Pantau perubahan halaman aktif
watch(currentPage, () => {
  loadTransactions()
})

onMounted(() => {
  loadTransactions()
  fetchCoas()
})

const handleFormSubmit = async (formData: any) => {
  const success = await createTransaction(formData)
  if (success) {
    showModal.value = false
    loadTransactions() // Refresh data setelah input baru
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Transactions</h1>
        <p class="text-slate-500 text-sm mt-1">History of your journal debits and credits.</p>
      </div>
      <button
        @click="showModal = true"
        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 transition"
      >
        <span>+</span> Record New Transaction
      </button>
    </div>

    <!-- Filters Block (Date, Account, Description) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 grid gap-4 md:grid-cols-4">
      <!-- Filter: From Date -->
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">From Date</label>
        <input
          v-model="fromDate"
          type="date"
          class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none"
        />
      </div>

      <!-- Filter: To Date -->
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">To Date</label>
        <input
          v-model="toDate"
          type="date"
          class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none"
        />
      </div>

      <!-- Filter: Account (COA) -->
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Filter by Account</label>
        <select
          v-model="coaId"
          class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none bg-white"
        >
          <option value="">All Accounts</option>
          <option v-for="c in coas" :key="c.id" :value="c.id">
            {{ c.code }} - {{ c.name }}
          </option>
        </select>
      </div>

      <!-- Filter: Description Search -->
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search Description</label>
        <input
          v-model="search"
          type="text"
          placeholder="Search by description..."
          class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none"
        />
      </div>
    </div>

    <!-- State Loading -->
    <LoadingSpinner v-if="loading" message="Syncing ledger transactions..." />

    <!-- Transaction Ledger Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[700px]">
        <thead>
          <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
            <th class="px-6 py-4">Date</th>
            <th class="px-6 py-4">Account (COA)</th>
            <th class="px-6 py-4">Description</th>
            <th class="px-6 py-4 text-right">Debit</th>
            <th class="px-6 py-4 text-right">Credit</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
          <tr v-for="t in transactions" :key="t.id" class="hover:bg-slate-50/50 transition">
            <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(t.transaction_date) }}</td>
            <td class="px-6 py-4">
              <div class="font-semibold text-slate-800">{{ t.coa?.name }}</div>
              <div class="text-xs text-slate-400 font-mono">{{ t.coa?.code }}</div>
            </td>
            <td class="px-6 py-4 text-slate-600">{{ t.description }}</td>
            <td class="px-6 py-4 text-right font-semibold text-rose-600">
              {{ Number(t.debit) > 0 ? formatRupiah(t.debit) : '-' }}
            </td>
            <td class="px-6 py-4 text-right font-semibold text-emerald-600">
              {{ Number(t.credit) > 0 ? formatRupiah(t.credit) : '-' }}
            </td>
          </tr>
          <tr v-if="transactions.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
              No transactions recorded yet.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <div v-if="pagination.total > 0" class="no-print flex flex-wrap items-center justify-between gap-4 bg-white px-6 py-4 rounded-2xl shadow-sm border border-slate-100">
      <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
        <div>
          Showing Page <span class="font-bold text-slate-800">{{ pagination.current_page }}</span> of <span class="font-bold text-slate-800">{{ pagination.last_page }}</span> (<span class="font-medium text-slate-700">{{ pagination.total }}</span> entries)
        </div>
        
        <!-- Dropdown Jumlah Baris Per Halaman -->
        <div class="flex items-center gap-2">
          <span>Show:</span>
          <select 
            v-model="perPage" 
            class="rounded-lg border border-slate-200 px-2 py-1 text-slate-700 bg-white focus:outline-none focus:border-emerald-500"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1 || loading"
          class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition disabled:opacity-50 disabled:hover:bg-transparent"
        >
          Previous
        </button>
        <button
          @click="currentPage++"
          :disabled="currentPage === pagination.last_page || loading"
          class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition disabled:opacity-50 disabled:hover:bg-transparent"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Modal Form Transaksi -->
    <Modal
      :show="showModal"
      title="Record New Journal Entry"
      @close="showModal = false"
    >
      <TransactionForm
        :errors="errors"
        :loading="loading"
        @submit="handleFormSubmit"
        @cancel="showModal = false"
      />
    </Modal>
  </div>
</template>
