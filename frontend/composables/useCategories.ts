import { ref } from 'vue'
import type { Category, CategoryType } from '~/types/category'

export const useCategories = () => {
    const api = useApi() // Menggunakan HTTP helper dari utils/api.ts
    const toast = useToast() // Menggunakan toast untuk notifikasi sukses/gagal

    const categories = ref<Category[]>([])
    const loading = ref<boolean>(false)
    const errors = ref<Record<string, string[]>>({})

    // 1. GET: Ambil semua data kategori dari backend
    const fetchCategories = async (search?: string) => {
        loading.value = true
        try {
            const response = await api<{ data: Category[] }>('/categories', {
                params: search ? { search } : undefined
            })
            categories.value = response.data
        } catch (err: any) {
            toast.danger('Gagal mengambil data kategori.')
        } finally {
            loading.value = false
        }
    }

    // 2. POST: Tambah kategori baru
    const createCategory = async (data: { name: string; type: CategoryType }) => {
        loading.value = true
        errors.value = {}
        try {
            await api('/categories', {
                method: 'POST',
                body: data
            })
            toast.success('Kategori berhasil ditambahkan!')
            await fetchCategories() // Segarkan daftar kategori
            return true
        } catch (err: any) {
            if (err.status === 422) {
                errors.value = err.data.errors // Ambil error validasi dari Laravel
            } else {
                toast.danger('Gagal menambahkan kategori.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    // 3. PUT: Perbarui kategori
    const updateCategory = async (id: number, data: { name: string; type: CategoryType }) => {
        loading.value = true
        errors.value = {}
        try {
            await api(`/categories/${id}`, {
                method: 'PUT',
                body: data
            })
            toast.success('Kategori berhasil diperbarui!')
            await fetchCategories()
            return true
        } catch (err: any) {
            if (err.status === 422) {
                errors.value = err.data.errors
            } else {
                toast.danger('Gagal memperbarui kategori.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    // 4. DELETE: Hapus kategori
    const deleteCategory = async (id: number) => {
        loading.value = true
        try {
            const response = await api<{ message: string }>(`/categories/${id}`, {
                method: 'DELETE'
            })
            toast.success(response.message || 'Kategori berhasil dihapus.')
            await fetchCategories()
            return true
        } catch (err: any) {
            // Menangkap respon kegagalan hapus (karena kategori sedang digunakan di COA)
            if (err.status === 422) {
                toast.danger(err.data.message)
            } else {
                toast.danger('Gagal menghapus kategori.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    return {
        categories,
        loading,
        errors,
        fetchCategories,
        createCategory,
        updateCategory,
        deleteCategory
    }
}
