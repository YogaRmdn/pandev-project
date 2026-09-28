import HeroSection from "./_components/hero-section";
import ServicesSection from "./_components/services-section";
import TechStacksSection from "./_components/tech-stacks-section";
import CtaSection from "./_components/cta-section";

export default function Home() {
  return (
    <div className="flex flex-col min-h-screen">
      <main className="flex-1 space-y-8">
        <HeroSection />
        <ServicesSection />
        <TechStacksSection />
        <CtaSection />
      </main>
    </div>
  );
}
