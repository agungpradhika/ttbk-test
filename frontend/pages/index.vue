<script setup lang="ts">
import { onMounted, watch, ref } from 'vue'
import type { ProfitLossReport } from '~/types/report'

definePageMeta({
  title: 'Dashboard'
})

const config = useRuntimeConfig()

// 1. Fetch Ringkasan Statistik Laba Rugi
const { data: report, pending, error } = await useFetch<{ data: ProfitLossReport }>('/profit-loss', {
  baseURL: config.public.apiBase
})

// 2. Fetch Data Grafik Bulanan dengan tahun dinamis
const selectedYear = ref(2026)
const { data: chartResponse } = await useFetch<{ labels: string[], income: number[], expense: number[] }>(
  () => `/dashboard/chart?year=${selectedYear.value}`, 
  { baseURL: config.public.apiBase }
)

// 3. Fetch Transaksi Terakhir (ambil 5 teratas)
const { data: recentTransactions } = await useFetch<{ data: any[] }>('/transactions', {
  baseURL: config.public.apiBase
})

// Helper format uang Rupiah
const formatRupiah = (val: any) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(Number(val || 0))
}

// Format tanggal
const formatDateString = (dateStr: string) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

// Fungsi memuat Chart.js secara dinamis untuk menghindari race-condition
const loadChartJs = (callback: () => void) => {
  if (typeof window === 'undefined') return
  if ((window as any).Chart) {
    callback()
    return
  }
  const script = document.createElement('script')
  script.src = 'https://cdn.jsdelivr.net/npm/chart.js'
  script.async = true
  script.onload = () => {
    callback()
  }
  document.head.appendChild(script)
}

// Menggambar grafik Chart.js
let chartInstance: any = null

const renderChart = () => {
  const ctx = document.getElementById('dashboardChart') as HTMLCanvasElement | null
  if (!ctx || !(window as any).Chart || !chartResponse.value) return

  if (chartInstance) {
    chartInstance.destroy()
  }

  chartInstance = new (window as any).Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartResponse.value.labels,
      datasets: [
        {
          label: 'Income',
          data: chartResponse.value.income,
          backgroundColor: '#10b981', // emerald-500
          borderRadius: 6,
          borderWidth: 0,
        },
        {
          label: 'Expense',
          data: chartResponse.value.expense,
          backgroundColor: '#f43f5e', // rose-500
          borderRadius: 6,
          borderWidth: 0,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            usePointStyle: true,
            boxWidth: 8
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: '#f1f5f9'
          },
          ticks: {
            callback: (val: any) => 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'K'
          }
        }
      }
    }
  })
}

onMounted(() => {
  loadChartJs(() => {
    renderChart()
  })
})

// Gambar ulang chart jika data API diperbarui
watch(chartResponse, () => {
  renderChart()
})
</script>

<template>
  <div class="space-y-8">
    <!-- Header Welcome -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">
          Dashboard Overview
        </h1>
        <p class="mt-1 text-slate-500 text-sm">
          Real-time overview of your financial activity from the database.
        </p>
      </div>
    </div>

    <!-- State: Loading -->
    <LoadingSpinner v-if="pending" message="Loading financial overview..." />

    <!-- State: Error -->
    <div v-else-if="error" class="rounded-xl bg-red-50 p-6 text-red-700 border border-red-200">
      <p class="font-semibold">Failed to load financial data</p>
      <p class="text-sm mt-1">Please make sure your Laravel backend server is running at {{ config.public.apiBase }}</p>
    </div>

    <!-- State: Success -->
    <div v-else class="space-y-8">
      <!-- 3 Key Cards -->
      <div class="grid gap-6 md:grid-cols-3">
        <!-- Card: Income -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">
              Total Income
            </p>
            <p class="mt-2 text-2xl font-bold text-emerald-600 font-mono">
              {{ formatRupiah(report?.data?.income) }}
            </p>
          </div>
          <span class="text-3xl bg-emerald-50 p-3 rounded-xl">💰</span>
        </div>

        <!-- Card: Expense -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">
              Total Expense
            </p>
            <p class="mt-2 text-2xl font-bold text-rose-600 font-mono">
              {{ formatRupiah(report?.data?.expense) }}
            </p>
          </div>
          <span class="text-3xl bg-rose-50 p-3 rounded-xl">💸</span>
        </div>

        <!-- Card: Net Profit -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">
              Net Profit
            </p>
            <p 
              class="mt-2 text-2xl font-bold font-mono"
              :class="((report?.data?.net_profit ?? 0) >= 0) ? 'text-blue-600' : 'text-rose-700'"
            >
              {{ formatRupiah(report?.data?.net_profit) }}
            </p>
          </div>
          <span class="text-3xl bg-blue-50 p-3 rounded-xl">📈</span>
        </div>
      </div>

      <!-- Grid: Grafik Batang & Recent Transactions -->
      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Panel Grafik (Kiri) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm lg:col-span-2">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800">Monthly Financial Chart</h3>
            <select
              v-model="selectedYear"
              class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm bg-white focus:outline-none focus:border-emerald-500 text-slate-700 font-semibold"
            >
              <option :value="2024">Year 2024</option>
              <option :value="2025">Year 2025</option>
              <option :value="2026">Year 2026</option>
              <option :value="2027">Year 2027</option>
            </select>
          </div>
          <div class="h-80 relative">
            <canvas id="dashboardChart"></canvas>
          </div>
        </div>

        <!-- Panel Recent Transactions (Kanan) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
          <div>
            <h3 class="text-base font-bold text-slate-800 mb-4">Recent Transactions</h3>
            <div class="space-y-4">
              <div 
                v-for="t in recentTransactions?.data?.slice(0, 5) || []" 
                :key="t.id"
                class="flex items-center justify-between border-b border-slate-50 pb-3"
              >
                <div>
                  <p class="text-sm font-semibold text-slate-800 truncate max-w-[150px]">{{ t.description }}</p>
                  <p class="text-[10px] text-slate-400 font-mono">{{ formatDateString(t.transaction_date) }}</p>
                </div>
                <div class="text-right">
                  <p 
                    v-if="Number(t.credit) > 0" 
                    class="text-xs font-bold text-emerald-600 font-mono"
                  >
                    +{{ formatRupiah(t.credit) }}
                  </p>
                  <p 
                    v-else 
                    class="text-xs font-bold text-rose-600 font-mono"
                  >
                    -{{ formatRupiah(t.debit) }}
                  </p>
                </div>
              </div>

              <div 
                v-if="!recentTransactions?.data || recentTransactions.data.length === 0" 
                class="text-center py-8 text-xs text-slate-400"
              >
                No transactions recorded yet.
              </div>
            </div>
          </div>

          <NuxtLink 
            to="/transactions"
            class="text-xs text-center font-bold text-emerald-600 hover:text-emerald-700 hover:underline pt-4 block border-t border-slate-100 mt-2"
          >
            View All Transactions ➜
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>
