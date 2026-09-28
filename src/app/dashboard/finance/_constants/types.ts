export const FINANCE_TYPES = ["INCOME", "EXPENSE"] as const;

export type FinanceType = (typeof FINANCE_TYPES)[number];
