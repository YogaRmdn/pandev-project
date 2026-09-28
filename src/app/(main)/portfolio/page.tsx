import type { Metadata } from "next";
import Image from "next/image";
import Carousel from "./_components/carousel";
import { Button } from "@/components/ui/button";
import { MoveUpRightIcon } from "lucide-react";
import CtaSection from "../_components/cta-section";

export const metadata: Metadata = {
  title: "Portofolio",
  description:
    "Lihat proyek dan karya terbaik yang telah dikerjakan oleh PanDev.",
};

import Link from "next/link";

const FEATURED_PROJECTS = [
  {
    id: 1,
    title: "Proyek Web Application",
    image: "/assets/common/hero-image.svg",
  },
  {
    id: 2,
    title: "Proyek Mobile App",
    image: "/assets/common/hero-image.svg",
  },
  {
    id: 3,
    title: "Proyek IoT",
    image: "/assets/common/hero-image.svg",
  },
  {
    id: 4,
    title: "Proyek Data Analytics",
    image: "/assets/common/hero-image.svg",
  },
  {
    id: 5,
    title: "Proyek Cyber Security",
    image: "/assets/common/hero-image.svg",
  },
];

export default function PortofolioPage() {
  return (
    <div className="flex flex-col min-h-screen">
      <main className="flex-1 space-y-4">
        <section className="flex flex-col items-center flex-1 w-full py-16">
          <h1 className="text-3xl font-bold tracking-tight text-center uppercase font-heading sm:text-4xl text-primary">
            Project showcase
          </h1>
          <Carousel />
          <p className="max-w-4xl mt-4 text-base text-center text-muted-foreground lg:text-xl">
            Kumpulan proyek aplikasi dalam bentuk website, mobile, dan desktop
            serta design yang telah kami kerjakan dan bangun
          </p>
          <Button className="px-4 mt-4 h-14 rounded-2xl" asChild>
            <Link href="/portfolio/list">
              Lihat Selengkapnya <MoveUpRightIcon />
            </Link>
          </Button>
        </section>

        <section className="px-4 space-y-8">
          <div className="text-4xl font-bold uppercase text-primary">
            Recent Project
          </div>
          
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            {FEATURED_PROJECTS.slice(0, 2).map((project) => (
              <div
                key={project.id}
                className="relative overflow-hidden group aspect-video rounded-xl bg-muted"
              >
                <Image
                  src={project.image}
                  alt={project.title}
                  fill
                  className="object-cover transition-transform duration-300 group-hover:scale-105"
                />
                <div className="absolute inset-0 transition-opacity duration-300 opacity-0 bg-gradient-to-t from-black/60 to-transparent group-hover:opacity-100" />
                <div className="absolute bottom-0 left-0 p-4 transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                  <h3 className="text-lg font-semibold text-white">
                    {project.title}
                  </h3>
                </div>
              </div>
            ))}
          </div>

          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            {FEATURED_PROJECTS.slice(2).map((project) => (
              <div
                key={project.id}
                className="relative overflow-hidden group aspect-square rounded-xl bg-muted"
              >
                <Image
                  src={project.image}
                  alt={project.title}
                  fill
                  className="object-cover transition-transform duration-300 group-hover:scale-105"
                />
                <div className="absolute inset-0 transition-opacity duration-300 opacity-0 bg-gradient-to-t from-black/60 to-transparent group-hover:opacity-100" />
                <div className="absolute bottom-0 left-0 p-4 transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                  <h3 className="text-lg font-semibold text-white">
                    {project.title}
                  </h3>
                </div>
              </div>
            ))}
          </div>
        </section>

        <CtaSection />
      </main>
    </div>
  );
}
