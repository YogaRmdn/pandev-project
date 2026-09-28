import DashboardHeader from "./_components/dashboard-header";
import { Suspense } from "react";
import StatsCards from "./_components/stats-cards";


export default function DashboardPage() {
  return (
    <div className="space-y-4">
      <DashboardHeader title="Dashboard" description="Kelola portfolio yang ingin Anda tampilkan" />
      <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
        <Suspense>
          <StatsCards />
        </Suspense>
      </div>
    </div>
  );
}
