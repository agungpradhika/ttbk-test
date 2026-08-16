import type { Category } from "./category";

export interface ChartOfAccount {
    id: number
    code: string
    name: string
    category_id: number
    category?: Category // relasi opsional ke kategori
    created_at: string
    updated_at: string
}