<script setup>
const config = useRuntimeConfig()

// Mengambil data laba rugi dari API backend Laravel
const { data: report, pending, error } = await useFetch('/profit-loss', {
  baseURL: config.public.apiBase
})

// Helper format uang Rupiah
const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(Number(val || 0))
}
</script>

<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">
        Dashboard
      </h1>

      <p class="mt-1 text-gray-500">
        Ringkasan aktivitas keuangan secara real-time dari database.
      </p>
    </div>

    <!-- State: Loading -->
    <div v-if="pending" class="text-gray-500 py-4">
      Memuat data dari server...
    </div>

    <!-- State: Error -->
    <div v-else-if="error" class="rounded-xl bg-red-50 p-6 text-red-700 border border-red-200">
      <p class="font-semibold">Gagal memuat data keuangan</p>
      <p class="text-sm mt-1">Pastikan server Laravel backend Anda sudah aktif di {{ config.public.apiBase }}</p>
    </div>

    <!-- State: Success -->
    <div v-else class="grid gap-6 md:grid-cols-3">
      <!-- Card: Pemasukan -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">
          Total Pendapatan (Income)
        </p>
        <p class="mt-2 text-2xl font-bold text-emerald-600">
          {{ formatRupiah(report?.data?.income) }}
        </p>
      </div>

      <!-- Card: Pengeluaran -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">
          Total Pengeluaran (Expense)
        </p>
        <p class="mt-2 text-2xl font-bold text-rose-600">
          {{ formatRupiah(report?.data?.expense) }}
        </p>
      </div>

      <!-- Card: Laba Bersih -->
      <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">
          Laba Bersih (Net Profit)
        </p>
        <p 
          class="mt-2 text-2xl font-bold"
          :class="(report?.data?.net_profit >= 0) ? 'text-blue-600' : 'text-rose-700'"
        >
          {{ formatRupiah(report?.data?.net_profit) }}
        </p>
      </div>
    </div>
  </div>
</template>