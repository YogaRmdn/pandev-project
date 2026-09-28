import DashboardHeader from "../_components/dashboard-header";
import TransactionContent from "./_components/transaction-content";

export default function FinancePage() {
  return (
    <section>
      <DashboardHeader
        title="Keuangan"
        description="Kelola data pemasukan dan pengeluaran"
      />
      <div className="p-4">
        <TransactionContent />
      </div>
    </section>
  );
}
