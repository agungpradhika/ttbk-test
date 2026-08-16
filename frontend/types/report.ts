export interface ProfitLossReport {
    period: {
        from: string
        to: string
    }
    income: number
    expense: number
    net_profit: number
}
