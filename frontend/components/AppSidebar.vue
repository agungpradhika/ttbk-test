<script setup lang="ts">
defineProps<{
  isOpen: boolean
}>()

defineEmits(['close'])
</script>

<template>
  <div class="lg:w-64 lg:flex-shrink-0">
    <!-- Backdrop Overlay (Hanya aktif di mobile saat sidebar terbuka) -->
    <div 
      v-if="isOpen" 
      class="fixed inset-0 bg-slate-950/60 z-30 lg:hidden transition-opacity duration-300"
      @click="$emit('close')"
    ></div>

    <!-- Sidebar Container -->
    <aside 
      class="w-64 bg-slate-900 text-slate-100 flex flex-col min-h-screen border-r border-slate-800 fixed inset-y-0 left-0 z-40 transform lg:transform-none transition-transform duration-300 ease-in-out no-print"
      :class="isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:transform-none'"
    >
      <!-- Logo/Brand & Close Button -->
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <h1 class="text-xl font-bold tracking-wider text-emerald-400 flex items-center gap-2.5">
          <!-- Simple Geometric Logo -->
          <svg class="h-6 w-6 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3" />
          </svg>
          <span>TBK</span>
        </h1>
        
        <!-- Close Button (Hanya tampil di mobile) -->
        <button 
          @click="$emit('close')"
          class="lg:hidden p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition focus:outline-none"
          aria-label="Close Sidebar"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <NuxtLink
          to="/"
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white group"
          active-class="bg-emerald-600 !text-white font-semibold"
        >
          <!-- Grid/Dashboard Icon -->
          <svg class="h-5 w-5 opacity-75 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          <span>Dashboard</span>
        </NuxtLink>

        <NuxtLink
          to="/categories"
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white group"
          active-class="bg-emerald-600 !text-white font-semibold"
        >
          <!-- Tag/Categories Icon -->
          <svg class="h-5 w-5 opacity-75 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>Categories</span>
        </NuxtLink>

        <NuxtLink
          to="/chart-of-accounts"
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white group"
          active-class="bg-emerald-600 !text-white font-semibold"
        >
          <!-- Folder/Classification COA Icon -->
          <svg class="h-5 w-5 opacity-75 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <span>Chart of Accounts</span>
        </NuxtLink>

        <NuxtLink
          to="/transactions"
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white group"
          active-class="bg-emerald-600 !text-white font-semibold"
        >
          <!-- Receipt/Document Ledger Icon -->
          <svg class="h-5 w-5 opacity-75 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
          <span>Transactions</span>
        </NuxtLink>

        <NuxtLink
          to="/profit-loss"
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white group"
          active-class="bg-emerald-600 !text-white font-semibold"
        >
          <!-- Trending Up/P&L Icon -->
          <svg class="h-5 w-5 opacity-75 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
          <span>Profit & Loss</span>
        </NuxtLink>
      </nav>
    </aside>
  </div>
</template>
