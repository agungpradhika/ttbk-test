import { ref } from 'vue'
import type { Transaction } from '~/types/transaction'

export const useTransactions = () => {
    const api = useApi()
    const toast = useToast()

    const transactions = ref<Transaction[]>([])
    const loading = ref<boolean>(false)
    const errors = ref<Record<string, string[]>>({})

    // 1. GET: Mengambil riwayat transaksi dari backend
    const fetchTransactions = async () => {
        loading.value = true
        try {
            const response = await api<{ data: Transaction[] }>('/transactions')
            transactions.value = response.data
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
        fetchTransactions,
        createTransaction
    }
}
