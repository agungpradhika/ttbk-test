import { ref } from 'vue'
import type { Transaction } from '~/types/transaction'

export const useTransactions = () => {
    const api = useApi()
    const toast = useToast()

    const transactions = ref<Transaction[]>([])
    const loading = ref<boolean>(false)
    const errors = ref<Record<string, string[]>>({})

    // Meta pagination untuk frontend
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 50
    })

    // 1. GET: Mengambil riwayat transaksi dari backend
    const fetchTransactions = async (filters?: {
        from?: string
        to?: string
        coa_id?: number | ''
        search?: string
        page?: number
        per_page?: number
    }) => {
        loading.value = true
        try {
            const response = await api<{ 
                data: Transaction[]
                meta: {
                    current_page: number
                    last_page: number
                    total: number
                    per_page: number
                }
            }>('/transactions', {
                params: filters
            })
            transactions.value = response.data
            if (response.meta) {
                pagination.value = response.meta
            }
        } catch (err: any) {
            toast.danger('Gagal mengambil riwayat transaksi.')
        } finally {
            loading.value = false
        }
    }

    // 2. POST: Mencatat transaksi baru
    const createTransaction = async (data: {
        transaction_date: string
        coa_id: number
        description: string
        debit: number
        credit: number
    }) => {
        loading.value = true
        errors.value = {}
        try {
            await api('/transactions', {
                method: 'POST',
                body: data
            })
            toast.success('Transaksi berhasil dicatat!')
            await fetchTransactions()
            return true
        } catch (err: any) {
            if (err.status === 422) {
                errors.value = err.data.errors // Mengambil error validasi aturan debit/kredit
            } else {
                toast.danger('Gagal mencatat transaksi.')
            }
            return false
        } finally {
            loading.value = false
        }
    }

    return {
        transactions,
        loading,
        errors,
        pagination,
        fetchTransactions,
        createTransaction
    }
}
