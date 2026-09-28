import { Card, CardContent } from "@/components/ui/card";
import { prisma } from "@/lib/prisma";
import { convertToIDR } from "@/lib/utils";
import { FolderIcon, TrendingDown, TrendingUp } from "lucide-react";

export default async function StatsCards() {
  const totalPortfolios = await prisma.portfolio.count();
  const transactions = await prisma.transaction.findMany({
   select: {
    type: true,
    amount: true
   }
  });
  let totalIncome = 0;
  let totalExpense = 0;
  transactions
    .filter((incomeItem) => incomeItem.type === "INCOME").map((item) => totalIncome += item.amount)
  transactions
    .filter((incomeItem) => incomeItem.type === "EXPENSE").map((item) => totalIncome += item.amount)
  return (
    <>
      <Card>
        <CardContent className="flex items-center gap-4">
          <div className="flex size-12 items-center justify-center rounded-lg bg-primary/10">
            <FolderIcon className="size-6 text-primary" />
          </div>
          <div>
            <div className="text-sm text-muted-foreground uppercase">
              Total Projek Diunggah
            </div>
            <div className="text-2xl font-bold">{totalPortfolios}</div>
          </div>
        </CardContent>
      </Card>
      <Card>
        <CardContent className="flex items-center gap-4">
          <div className="flex size-12 items-center justify-center rounded-lg bg-primary/10">
            <TrendingUp className="size-6 text-primary" />
          </div>
          <div>
            <div className="text-sm text-muted-foreground uppercase">
              Total Pendapatan
            </div>
            <div className="text-2xl font-bold">
              {convertToIDR(totalIncome)}
            </div>
          </div>
        </CardContent>
      </Card>
      <Card>
        <CardContent className="flex items-center gap-4">
          <div className="flex size-12 items-center justify-center rounded-lg bg-primary/10">
            <TrendingDown className="size-6 text-primary" />
          </div>
          <div>
            <div className="text-sm text-muted-foreground uppercase">
              Total Pengeluaran
            </div>
            <div className="text-2xl font-bold">
              {convertToIDR(totalExpense)}
            </div>
          </div>
        </CardContent>
      </Card>
    </>
  );
}
