export const useApi = () => {
    const config = useRuntimeConfig()

    // Mengambil fetch instance dengan base URL backend terintegrasi
    return $fetch.create({
        baseURL: config.public.apiBase,
        headers: {
            Accept: 'application/json'
        }
    })
}
