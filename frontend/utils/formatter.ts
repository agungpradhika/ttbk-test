// Format angka ke Rupiah
export const formatRupiah = (value: number | string | undefined | null): string => {
    const num = Number(value || 0)
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(num)
}

// Format tanggal standar YYYY-MM-DD ke format lokal Indonesia (contoh: 16 Agustus 2026)
export const formatDate = (dateString: string): string => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}
