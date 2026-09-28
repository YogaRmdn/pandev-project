import type { Metadata } from "next";
import TeamSection from "./_components/team-section";
import Image from "next/image";
import CtaSection from "../_components/cta-section";

export const metadata: Metadata = {
  title: "Tentang",
  description:
    "Kenali lebih dekat Arkana, software house di Indonesia yang fokus pada solusi teknologi.",
};

export default function TentangPage() {
  return (
    <main className="flex w-full flex-1 flex-col pt-16">
      <div className="flex flex-col sm:flex-row items-center justify-center gap-8">
        <Image
          width={300}
          height={300}
          alt="PanDev Logo"
          src={"/assets/common/logo.png"}
        />
        <div className="max-w-xl">
          <h1 className="font-heading text-3xl font-bold tracking-tight sm:text-3xl uppercase text-primary">
            Taking your ideas to next level
          </h1>
          <p className="mt-4 text-balance">
            PanDev adalah digital agency di Indonesia yang menawarkan solusi
            aplikasi untuk bisnis dan individu. Kami membantu mewujudkan ide
            menjadi produk digital yang bermanfaat dan berkelanjutan. Serta
            beragam produk digital lainnya untuk memenuhi kebutuhan Anda
          </p>
        </div>
      </div>
      <TeamSection />
      <CtaSection />
    </main>
  );
}
