export interface ProfitLossReport {
    period: {
        from: string
        to: string
    }
    income: number
    expense: number
    net_profit: number
    categories: Array<{
        id: number
        name: string
        type: 'income' | 'expense'
        total: number
    }>
}
