<template>
  <aside class="w-64 bg-slate-900 text-slate-100 flex flex-col min-h-screen border-r border-slate-800">
    <!-- Logo/Brand -->
    <div class="p-6 border-b border-slate-800">
      <h1 class="text-xl font-bold tracking-wider text-emerald-400 flex items-center gap-2">
        <span>📖</span> TBK
      </h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1">
      <NuxtLink
        to="/"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white"
        active-class="bg-emerald-600 !text-white font-semibold"
      >
        <span>📊</span> Dashboard
      </NuxtLink>

      <NuxtLink
        to="/categories"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white"
        active-class="bg-emerald-600 !text-white font-semibold"
      >
        <span>🏷️</span> Categories
      </NuxtLink>

      <NuxtLink
        to="/chart-of-accounts"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white"
        active-class="bg-emerald-600 !text-white font-semibold"
      >
        <span>🗂️</span> Chart of Accounts
      </NuxtLink>

      <NuxtLink
        to="/transactions"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white"
        active-class="bg-emerald-600 !text-white font-semibold"
      >
        <span>💸</span> Transactions
      </NuxtLink>

      <NuxtLink
        to="/profit-loss"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white"
        active-class="bg-emerald-600 !text-white font-semibold"
      >
        <span>📈</span> Profit & Loss
      </NuxtLink>
    </nav>
  </aside>
</template>
