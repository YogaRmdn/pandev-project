"use client";

import { getPortfolioByUuid } from "@/app/actions/portfolio";
import { useQuery } from "@tanstack/react-query";
import { useParams } from "next/navigation";
import PortfolioDetail from "../_components/portfolio-detail";
import PortfolioDetailSkeleton from "../_components/portfolio-detail-skeleton";
import CtaSection from "../../_components/cta-section";
import { useSession } from "@/lib/auth-client";
import { Button } from "@/components/ui/button";
import Link from "next/link";

export default function PortfolioDetailPage() {
  const params = useParams();
  const uuid = params.uuid as string;
  const { data: session } = useSession();

  const { data: result, isLoading } = useQuery({
    queryKey: ["portfolio", uuid],
    queryFn: () => getPortfolioByUuid(uuid),
    enabled: !!uuid,
  });

  const portfolio = result?.success ? result.data : null;
  const isDraft = portfolio?.status === "draft";
  const isAuthenticated = !!session?.user;

  if (isLoading) {
    return <PortfolioDetailSkeleton />;
  }

  if (!portfolio) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="text-center">
          <h1 className="text-2xl font-bold">Portofolio Tidak Ditemukan</h1>
          <p className="mt-2 text-muted-foreground">
            Portofolio yang Anda cari tidak tersedia.
          </p>
        </div>
      </div>
    );
  }

  if (isDraft && !isAuthenticated) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="text-center">
          <h1 className="text-2xl font-bold">Portofolio Tidak Tersedia</h1>
          <p className="mt-2 text-muted-foreground">
            Portofolio ini masih dalam status draft dan hanya dapat diakses oleh
            pengguna yang telah login.
          </p>
          <Button asChild className="mt-4">
            <Link href="/dashboard">Login untuk mengakses</Link>
          </Button>
        </div>
      </div>
    );
  }

  return (
    <div className="mt-4">
      <div className="p-4">
        {portfolio.status === "draft" && (
          <div className="px-3 py-1 text-lg font-medium bg-amber-100 text-amber-800 mb-4 rounded-lg w-fit mx-auto">
            Portofolio ini masih dalam status draft, Portfolio ini tidak akan ditampilkan di halaman publik
          </div>
        )}
        <div className="text-xl font-bold uppercase text-primary md:text-4xl">
          Portfolio Detail
        </div>
        <PortfolioDetail portfolio={portfolio} />
      </div>
      <CtaSection />
    </div>
  );
}
