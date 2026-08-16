<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  title: 'Profit & Loss'
})

const { report, loading, fetchReport } = useProfitLoss()

// State tanggal filter (default: awal bulan ini s/d hari ini)
const today = new Date()
const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 2).toISOString().split('T')[0]
const lastDay = today.toISOString().split('T')[0]

const fromDate = ref(firstDayOfMonth)
const toDate = ref(lastDay)

const loadReport = () => {
  fetchReport({
    from: fromDate.value,
    to: toDate.value
  })
}

// Filter kategori secara dinamis di frontend
const incomeCategories = computed(() => {
  return report.value?.categories?.filter(c => c.type === 'income') || []
})

const expenseCategories = computed(() => {
  return report.value?.categories?.filter(c => c.type === 'expense') || []
})

// Fungsi print PDF aman
const printReport = () => {
  if (import.meta.client) {
    window.print()
  }
}

// Fungsi export Excel aman
const exportToExcel = () => {
  if (!report.value) return

  // Format rincian baris kategori untuk Excel dengan format Rupiah
  const incomeRows = incomeCategories.value.map(cat => `
    <tr>
      <td style="padding-left: 20px; border: 1px solid #ccc;">${cat.name}</td>
      <td class="value">${formatRupiah(cat.total)}</td>
    </tr>
  `).join('')

  const expenseRows = expenseCategories.value.map(cat => `
    <tr>
      <td style="padding-left: 20px; border: 1px solid #ccc;">${cat.name}</td>
      <td class="value">(${formatRupiah(cat.total)})</td>
    </tr>
  `).join('')

  // Format data Excel dalam bentuk tabel HTML
  const excelContent = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
      <meta charset="utf-8">
      <style>
        .title { font-size: 16px; font-weight: bold; text-align: center; }
        .subtitle { font-size: 12px; text-align: center; color: #555; }
        .header { font-weight: bold; background-color: #f2f2f2; border: 1px solid #ccc; }
        .section-header { font-weight: bold; background-color: #e2e8f0; border: 1px solid #ccc; }
        .label { border: 1px solid #ccc; font-weight: bold; }
        .value { border: 1px solid #ccc; text-align: right; }
        .total-row { font-weight: bold; background-color: #f8fafc; border-top: 1px solid #000; }
        .profit { font-weight: bold; color: #2563eb; border-top: 2px double #000; border-bottom: 2px double #000; text-align: right; }
        .loss { font-weight: bold; color: #dc2626; border-top: 2px double #000; border-bottom: 2px double #000; text-align: right; }
      </style>
    </head>
    <body>
      <table>
        <tr>
          <td colspan="2" class="title">PROFIT & LOSS STATEMENT</td>
        </tr>
        <tr>
          <td colspan="2" class="title">Trans Berjaya Khatulistiwa</td>
        </tr>
        <tr>
          <td colspan="2" class="subtitle">Period: ${formatDate(fromDate.value)} to ${formatDate(toDate.value)}</td>
        </tr>
        <tr><td></td></tr>
        <tr class="header">
          <td style="width: 250px;">Account Category</td>
          <td style="width: 150px; text-align: right;">Total Amount (IDR)</td>
        </tr>
        
        <!-- Income Section -->
        <tr class="section-header">
          <td colspan="2">1. Income</td>
        </tr>
        ${incomeRows}
        <tr class="total-row">
          <td>Total Income</td>
          <td style="text-align: right;">${formatRupiah(report.value.income)}</td>
        </tr>

        <tr><td></td></tr>

        <!-- Expense Section -->
        <tr class="section-header">
          <td colspan="2">2. Expense</td>
        </tr>
        ${expenseRows}
        <tr class="total-row">
          <td>Total Expense</td>
          <td style="text-align: right;">(${formatRupiah(report.value.expense)})</td>
        </tr>

        <tr><td></td></tr>
        
        <tr>
          <td class="label" style="border-top: 2px double #000; border-bottom: 2px double #000;">Net Profit (Loss)</td>
          <td class="${report.value.net_profit >= 0 ? 'profit' : 'loss'}">${formatRupiah(report.value.net_profit)}</td>
        </tr>
      </table>
    </body>
    </html>
  `

  const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `Profit_Loss_Report_${fromDate.value}_to_${toDate.value}.xls`
  a.click()
  URL.revokeObjectURL(url)
}

onMounted(() => {
  loadReport()
})
</script>

<template>
  <div class="space-y-6">
    <!-- Date Filter Section -->
    <div class="no-print bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-wrap items-end gap-4 justify-between">
      <div class="flex flex-wrap items-center gap-4">
        <!-- From Date -->
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">From Date</label>
          <input
            v-model="fromDate"
            type="date"
            class="rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none"
          />
        </div>

        <!-- To Date -->
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">To Date</label>
          <input
            v-model="toDate"
            type="date"
            class="rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none"
          />
        </div>

        <!-- Filter Button -->
        <button
          @click="loadReport"
          :disabled="loading"
          class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-5 py-2 rounded-xl text-sm transition disabled:opacity-50"
        >
          {{ loading ? 'Processing...' : 'Filter Report' }}
        </button>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2">
        <button
          @click="exportToExcel"
          class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition"
        >
          📊 Export Excel
        </button>
        <button
          @click="printReport"
          class="bg-white hover:bg-slate-50 text-slate-700 font-semibold px-4 py-2 rounded-xl text-sm border border-slate-200 transition"
        >
          🖨️ Print PDF
        </button>
      </div>
    </div>

    <!-- State Loading -->
    <LoadingSpinner v-if="loading" message="Calculating Profit & Loss statement..." />

    <!-- Accounting Statement Sheet (Target area for clean printing) -->
    <div v-else class="print-sheet bg-white rounded-2xl shadow-sm border border-slate-100 p-8 max-w-2xl mx-auto space-y-8">
      <!-- Kop Surat Laporan -->
      <div class="text-center border-b border-slate-200 pb-6">
        <h2 class="text-2xl font-bold text-slate-800">Profit & Loss Statement</h2>
        <p class="text-emerald-600 font-bold text-sm tracking-wide mt-1 uppercase">Trans Berjaya Khatulistiwa</p>
        <p class="text-slate-400 text-xs mt-2 font-medium">
          Period: <span class="font-semibold text-slate-600">{{ formatDate(fromDate) }}</span> to 
          <span class="font-semibold text-slate-600">{{ formatDate(toDate) }}</span>
        </p>
      </div>

      <!-- Detail Perhitungan Laba Rugi -->
      <div class="space-y-6 text-sm text-slate-700">
        
        <!-- INCOME SECTION -->
        <div>
          <h3 class="font-bold text-slate-800 border-b border-slate-200 pb-2 uppercase tracking-wider text-xs">
            1. Income
          </h3>
          <!-- Rincian Kategori Dinamis -->
          <div class="divide-y divide-slate-50">
            <div 
              v-for="cat in incomeCategories" 
              :key="cat.id" 
              class="flex justify-between py-2 px-4 text-slate-600 hover:bg-slate-50/50"
            >
              <span>{{ cat.name }}</span>
              <span class="font-mono font-medium">{{ formatRupiah(cat.total) }}</span>
            </div>
            <div v-if="incomeCategories.length === 0" class="py-2 px-4 text-slate-400 text-xs">
              No income categories registered
            </div>
          </div>
          <!-- Subtotal Income -->
          <div class="flex justify-between py-3 px-2 font-bold text-slate-800 border-t border-slate-100 mt-2 bg-slate-50/40">
            <span>Total Income</span>
            <span class="text-emerald-600 font-mono">
              {{ formatRupiah(report?.income) }}
            </span>
          </div>
        </div>

        <!-- EXPENSE SECTION -->
        <div>
          <h3 class="font-bold text-slate-800 border-b border-slate-200 pb-2 uppercase tracking-wider text-xs mt-4">
            2. Expense
          </h3>
          <!-- Rincian Kategori Dinamis -->
          <div class="divide-y divide-slate-50">
            <div 
              v-for="cat in expenseCategories" 
              :key="cat.id" 
              class="flex justify-between py-2 px-4 text-slate-600 hover:bg-slate-50/50"
            >
              <span>{{ cat.name }}</span>
              <span class="font-mono font-medium">({{ formatRupiah(cat.total) }})</span>
            </div>
            <div v-if="expenseCategories.length === 0" class="py-2 px-4 text-slate-400 text-xs">
              No expense categories registered
            </div>
          </div>
          <!-- Subtotal Expense -->
          <div class="flex justify-between py-3 px-2 font-bold text-slate-800 border-t border-slate-100 mt-2 bg-slate-50/40">
            <span>Total Expense</span>
            <span class="text-rose-600 font-mono">
              ({{ formatRupiah(report?.expense) }})
            </span>
          </div>
        </div>

        <!-- NET PROFIT -->
        <div class="pt-4 mt-8 border-t-2 border-double border-slate-900">
          <div class="flex justify-between px-2 text-base font-bold">
            <span class="text-slate-800 uppercase tracking-wide">Net Profit (Loss)</span>
            <span 
              class="font-mono"
              :class="((report?.net_profit ?? 0) >= 0) ? 'text-blue-600' : 'text-rose-700'"
            >
              {{ formatRupiah(report?.net_profit) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Signature -->
      <div class="pt-12 flex justify-end text-xs text-slate-500">
        <div class="text-center space-y-16">
          <p>Prepared By, Finance Department</p>
          <div class="w-40 border-t border-slate-900 mx-auto"></div>
          <p class="font-bold text-slate-700">Staff Accounting</p>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
@media print {
  /* Sembunyikan sidebar utama Nuxt, AppHeader, dan Form filter */
  aside, 
  header, 
  .no-print,
  button {
    display: none !important;
  }
  
  /* Hilangkan background abu-abu body bawaan */
  body, 
  main {
    background-color: white !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Reset kontainer sheet agar pas di kertas A4 tanpa shadow */
  .print-sheet {
    border: none !important;
    box-shadow: none !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 auto !important;
  }
}
</style>
