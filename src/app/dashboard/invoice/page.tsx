import DashboardHeader from "../_components/dashboard-header";
import InvoiceContent from "./_components/invoice-content";

export default function InvoicePage() {
  return (
    <div>
      <DashboardHeader
        title="Faktur"
        description="Kelola faktur dan tagihan projek"
      />
      <div className="p-4">
        <InvoiceContent />
      </div>
    </div>
  );
}
