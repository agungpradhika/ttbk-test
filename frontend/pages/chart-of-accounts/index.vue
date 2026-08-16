<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import type { ChartOfAccount } from '~/types/chart-of-account'

definePageMeta({
  title: 'Chart of Accounts'
})

const {
  coas,
  loading,
  errors,
  fetchCoas,
  createCoa,
  updateCoa,
  deleteCoa
} = useChartOfAccounts()

const searchQuery = ref('')
const showModal = ref(false)
const selectedCoa = ref<ChartOfAccount | null>(null)

watch(searchQuery, (newVal) => {
  fetchCoas(newVal)
})

onMounted(() => {
  fetchCoas()
})

const openAddModal = () => {
  selectedCoa.value = null
  showModal.value = true
}

const openEditModal = (coa: ChartOfAccount) => {
  selectedCoa.value = coa
  showModal.value = true
}

const handleFormSubmit = async (formData: { code: string; name: string; category_id: number }) => {
  let success = false
  if (selectedCoa.value) {
    success = await updateCoa(selectedCoa.value.id, formData)
  } else {
    success = await createCoa(formData)
  }

  if (success) {
    showModal.value = false
  }
}

const showConfirmModal = ref(false)
const coaToDelete = ref<number | null>(null)

const handleDeletePrompt = (id: number) => {
  coaToDelete.value = id
  showConfirmModal.value = true
}

const handleConfirmDelete = async () => {
  if (coaToDelete.value !== null) {
    await deleteCoa(coaToDelete.value)
    coaToDelete.value = null
    showConfirmModal.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Chart of Accounts</h1>
        <p class="text-slate-500 text-sm mt-1">Manage and classify your ledger account codes.</p>
      </div>
      <button
        @click="openAddModal"
        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 transition"
      >
        <span>+</span> Add New Account
      </button>
    </div>

    <!-- Search Bar -->
    <div class="flex items-center gap-4">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search accounts by code or name..."
        class="w-full max-w-xs rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 bg-white"
      />
    </div>

    <!-- State Loading -->
    <LoadingSpinner v-if="loading" message="Syncing accounts..." />

    <!-- COA Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
            <th class="px-6 py-4">Account Code</th>
            <th class="px-6 py-4">Account Name</th>
            <th class="px-6 py-4">Category</th>
            <th class="px-6 py-4">Type</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
          <tr v-for="coa in coas" :key="coa.id" class="hover:bg-slate-50/50 transition">
            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ coa.code }}</td>
            <td class="px-6 py-4 font-semibold text-slate-800">{{ coa.name }}</td>
            <td class="px-6 py-4 text-slate-600">{{ coa.category?.name || '-' }}</td>
            <td class="px-6 py-4">
              <span
                v-if="coa.category"
                class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase"
                :class="{
                  'bg-emerald-50 text-emerald-700': coa.category.type === 'income',
                  'bg-rose-50 text-rose-700': coa.category.type === 'expense'
                }"
              >
                {{ coa.category.type }}
              </span>
              <span v-else>-</span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button
                @click="openEditModal(coa)"
                class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 font-semibold px-3 py-1.5 rounded-lg text-xs transition"
              >
                Edit
              </button>
              <button
                @click="handleDeletePrompt(coa.id)"
                class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-semibold px-3 py-1.5 rounded-lg text-xs transition"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="coas.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
              No accounts found. Click "Add New Account" to create one.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form COA -->
    <Modal
      :show="showModal"
      :title="selectedCoa ? 'Edit Account (COA)' : 'Create New Account'"
      @close="showModal = false"
    >
      <CoaForm
        :coa="selectedCoa"
        :errors="errors"
        :loading="loading"
        @submit="handleFormSubmit"
        @cancel="showModal = false"
      />
    </Modal>

    <!-- Modal Konfirmasi Hapus Custom -->
    <ConfirmModal
      :show="showConfirmModal"
      title="Delete Account (COA)"
      message="Are you sure you want to delete this Chart of Account? If this account has registered transactions in the ledger, the database will restrict this operation to maintain audit trail integrity."
      :loading="loading"
      @close="showConfirmModal = false"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>
