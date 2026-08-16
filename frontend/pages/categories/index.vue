<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import type { Category } from '~/types/category'

definePageMeta({
  title: 'Categories'
})

const {
  categories,
  loading,
  errors,
  fetchCategories,
  createCategory,
  updateCategory,
  deleteCategory
} = useCategories()

// State untuk pencarian & Modal
const searchQuery = ref('')
const showModal = ref(false)
const selectedCategory = ref<Category | null>(null) // null = tambah, object = edit

// Panggil fetch API ke Backend setiap kali query pencarian berubah
watch(searchQuery, (newVal) => {
  fetchCategories(newVal)
})

onMounted(() => {
  fetchCategories()
})

const openAddModal = () => {
  selectedCategory.value = null
  showModal.value = true
}

const openEditModal = (category: Category) => {
  selectedCategory.value = category
  showModal.value = true
}

const handleFormSubmit = async (formData: { name: string; type: any }) => {
  let success = false
  if (selectedCategory.value) {
    // Jalankan Edit (PUT) jika selectedCategory tidak null
    success = await updateCategory(selectedCategory.value.id, formData)
  } else {
    // Jalankan Tambah Baru (POST)
    success = await createCategory(formData)
  }

  if (success) {
    showModal.value = false
  }
}

const showConfirmModal = ref(false)
const categoryToDelete = ref<number | null>(null)

const handleDeletePrompt = (id: number) => {
  categoryToDelete.value = id
  showConfirmModal.value = true
}

const handleConfirmDelete = async () => {
  if (categoryToDelete.value !== null) {
    await deleteCategory(categoryToDelete.value)
    categoryToDelete.value = null
    showConfirmModal.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Page & Add button -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Categories</h1>
        <p class="text-slate-500 text-sm mt-1">Manage your income and expense categories.</p>
      </div>
      <button
        @click="openAddModal"
        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 transition"
      >
        <span>+</span> Add Category
      </button>
    </div>

    <!-- Search Bar -->
    <div class="flex items-center gap-4">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search categories..."
        class="w-full max-w-xs rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 bg-white"
      />
    </div>

    <!-- State Loading -->
    <LoadingSpinner v-if="loading" message="Syncing categories..." />

    <!-- Categories Data Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
            <th class="px-6 py-4">Category Name</th>
            <th class="px-6 py-4">Type</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
          <tr v-for="category in categories" :key="category.id" class="hover:bg-slate-50/50 transition">
            <td class="px-6 py-4 font-semibold text-slate-800">{{ category.name }}</td>
            <td class="px-6 py-4">
              <span
                class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase"
                :class="{
                  'bg-emerald-50 text-emerald-700': category.type === 'income',
                  'bg-rose-50 text-rose-700': category.type === 'expense'
                }"
              >
                {{ category.type }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button
                @click="openEditModal(category)"
                class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 font-semibold px-3 py-1.5 rounded-lg text-xs transition"
              >
                Edit
              </button>
              <button
                @click="handleDeletePrompt(category.id)"
                class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-semibold px-3 py-1.5 rounded-lg text-xs transition"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="categories.length === 0">
            <td colspan="3" class="px-6 py-8 text-center text-slate-400">
              No categories found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Reusable Modal Form -->
    <Modal
      :show="showModal"
      :title="selectedCategory ? 'Edit Category' : 'Add New Category'"
      @close="showModal = false"
    >
      <CategoryForm
        :category="selectedCategory"
        :errors="errors"
        :loading="loading"
        @submit="handleFormSubmit"
        @cancel="showModal = false"
      />
    </Modal>

    <!-- Modal Konfirmasi Hapus Custom -->
    <ConfirmModal
      :show="showConfirmModal"
      title="Delete Category"
      message="Are you sure you want to delete this category? If this category has registered accounts, the database might restrict the operation."
      :loading="loading"
      @close="showConfirmModal = false"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>
