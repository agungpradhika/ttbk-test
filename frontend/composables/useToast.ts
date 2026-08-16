import { ref } from 'vue'

// State notifikasi global (bersifat reaktif)
const message = ref<string | null>(null)
const type = ref<'success' | 'danger'>('success')
const show = ref<boolean>(false)
let timeoutId: any = null

export const useToast = () => {
    const showToast = (msg: string, toastType: 'success' | 'danger' = 'success') => {
        // Bersihkan timeout sebelumnya jika ada yang sedang berjalan
        if (timeoutId) clearTimeout(timeoutId)

        message.value = msg
        type.value = toastType
        show.value = true

        // Sembunyikan notifikasi setelah 3 detik secara otomatis
        timeoutId = setTimeout(() => {
            show.value = false
        }, 3000)
    }

    return {
        message,
        type,
        show,
        success: (msg: string) => showToast(msg, 'success'),
        danger: (msg: string) => showToast(msg, 'danger')
    }
}
