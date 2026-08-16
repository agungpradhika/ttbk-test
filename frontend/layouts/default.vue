<template>
  <div class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">
      <aside class="w-64 bg-gray-900 text-white">
        <div class="p-6">
          <h1 class="text-xl font-bold">
            Accounting
          </h1>
        </div>

        <nav class="px-4">
          <NuxtLink
            to="/"
            class="block rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"
          >
            Dashboard
          </NuxtLink>

          <NuxtLink
            to="/categories"
            class="block rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"
          >
            Categories
          </NuxtLink>

          <NuxtLink
            to="/chart-of-accounts"
            class="block rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"
          >
            Chart of Accounts
          </NuxtLink>

          <NuxtLink
            to="/transactions"
            class="block rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"
          >
            Transactions
          </NuxtLink>

          <NuxtLink
            to="/profit-loss"
            class="block rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"
          >
            Profit & Loss
          </NuxtLink>
        </nav>
      </aside>

      <main class="flex-1">
        <header class="border-b bg-white px-8 py-5">
          <h2 class="text-xl font-semibold text-gray-800">
            Accounting Dashboard
          </h2>
        </header>

        <div class="p-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>