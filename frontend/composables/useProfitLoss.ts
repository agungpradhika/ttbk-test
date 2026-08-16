import { ref } from 'vue'
import type { ProfitLossReport } from '~/types/report'

export const useProfitLoss = () => {
    const api = useApi()
    const toast = useToast()

    const report = ref<ProfitLossReport | null>(null)
    const loading = ref<boolean>(false)

    // Ambil laporan berdasarkan filter tanggal dari dan sampai (opsional)
    const fetchReport = async (filters?: { from?: string; to?: string }) => {
        loading.value = true
        try {
            const response = await api<{ data: ProfitLossReport }>('/profit-loss', {
                params: filters
            })
            report.value = response.data
        } catch (err: any) {
            toast.danger('Gagal memuat laporan laba rugi.')
        } finally {
            loading.value = false
        }
    }

    return {
        report,
        loading,
        fetchReport
    }
}
