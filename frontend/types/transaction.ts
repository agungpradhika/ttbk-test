export interface Transaction {
  id: number
  transaction_date: string
  description: string
  debit: number | string
  credit: number | string
  coa_id: number
  coa?: {
    id: number
    code: string
    name: string
  }
  category?: {
    id: number
    name: string
    type: string
  }
}
