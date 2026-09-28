import type { Metadata } from "next";
import { prisma } from "@/lib/prisma";
import PortfolioListWrapper from "./_components/portfolio-list-content";

export const metadata: Metadata = {
  title: "Daftar Portfolio | PanDev",
  description:
    "Lihat semua proyek dan karya terbaik yang telah dikerjakan oleh PanDev.",
};

async function getPublishedPortfolios() {
  try {
    const portfolios = await prisma.portfolio.findMany({
      where: { status: "published" },
      orderBy: { created_at: "desc" },
      include: { galery: true },
    });
    return portfolios;
  } catch (error) {
    console.error("Error fetching portfolios:", error);
    return [];
  }
}

export default async function PortfolioListPage() {
  const portfolios = await getPublishedPortfolios();

  return (
    <div className="flex flex-col min-h-screen">
      <main className="flex-1 space-y-4">
        <section className="py-16">
          <h1 className="text-3xl font-bold tracking-tight text-center uppercase font-heading sm:text-4xl text-primary">
            Semua Portfolio
          </h1>
          <p className="max-w-4xl mx-auto mt-4 text-base text-center text-muted-foreground lg:text-xl">
            Kumpulan proyek aplikasi dalam bentuk website, mobile, dan desktop
            serta design yang telah kami kerjakan dan bangun
          </p>
        </section>

        <section className="px-4 pb-16">
          <PortfolioListWrapper portfolios={portfolios} />
        </section>
      </main>
    </div>
  );
}
