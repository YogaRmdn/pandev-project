export const dynamic = "force-dynamic";

import PortfolioContent from "./_components/portfolio-content";
import DashboardHeader from "../_components/dashboard-header";

export default function PortfolioPage() {
  return (
    <div className="space-y-4">
      <DashboardHeader
        title="Portfolio"
        description="Kelola portfolio yang ingin Anda tampilkan"
      />
      <PortfolioContent />
    </div>
  );
}
