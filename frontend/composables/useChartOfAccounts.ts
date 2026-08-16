import { ref } from 'vue'
import type { ChartOfAccount } from '~/types/chart-of-account'

export const useChartOfAccounts = () => {
    const api = useApi()
    const toast = useToast()

    const coas = ref<ChartOfAccount[]>([])
    const loading = ref<boolean>(false)
    const errors = ref<Record<string, string[]>>({})

    // 1. GET: Mengambil daftar akun dari backend
    const fetchCoas = async () => {
        loading.value = true
        try {
            const response = await api<{ data: ChartOfAccount[] }>('/chart-of-accounts')
            coas.value = response.data
        } catch (err: any) {
            toast.danger('Gagal mengambil daftar akun (COA).')
        } finally {
            loading.value = false
        }
    }

    // 2. POST: Membuat akun baru
    const createCoa = async (data: { code: string; name: string; category_id: number }) => {
        loading.value = true
        errors.value = {}
        try {
            await api('/chart-of-accounts', {
                method: 'POST',
                body: data
            })
            toast.success('Akun (COA) berhasil dibuat!')
            await fetchCoas()
            return true
        } catch (err: any) {
            if (err.status === 422) {
                errors.value = err.data.errors
            } else {
                toast.danger('Gagal membuat akun.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    // 3. PUT: Memperbarui data akun
    const updateCoa = async (id: number, data: { code: string; name: string; category_id: number }) => {
        loading.value = true
        errors.value = {}
        try {
            await api(`/chart-of-accounts/${id}`, {
                method: 'PUT',
                body: data
            })
            toast.success('Akun (COA) berhasil diperbarui!')
            await fetchCoas()
            return true
        } catch (err: any) {
            if (err.status === 422) {
                errors.value = err.data.errors
            } else {
                toast.danger('Gagal memperbarui akun.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    // 4. DELETE: Menghapus akun
    const deleteCoa = async (id: number) => {
        loading.value = true
        try {
            const response = await api<{ message: string }>(`/chart-of-accounts/${id}`, {
                method: 'DELETE'
            })
            toast.success(response.message || 'Akun berhasil dihapus.')
            await fetchCoas()
            return true
        } catch (err: any) {
            if (err.status === 422) {
                toast.danger(err.data.message)
            } else {
                toast.danger('Gagal menghapus akun.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    return {
        coas,
        loading,
        errors,
        fetchCoas,
        createCoa,
        updateCoa,
        deleteCoa
    }
}
