import type { Metadata } from "next";

import {
  Card,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";

export const metadata: Metadata = {
  title: "Layanan",
  description:
    "Jelajahi layanan Arkana untuk bisnis dan individu, mulai dari aplikasi web hingga proyek IoT.",
};

const SERVICES = [
  {
    title: "Web Application",
    description:
      "Pengembangan aplikasi web modern, cepat, dan aman untuk kebutuhan bisnis Anda.",
  },
  {
    title: "Mobile Application",
    description:
      "Aplikasi mobile Android dan iOS yang responsif dan mudah digunakan.",
  },
  {
    title: "Desktop Application",
    description:
      "Aplikasi desktop yang stabil dan andal untuk operasional yang menuntut performa tinggi.",
  },
  {
    title: "Joki Tugas Sekolah & Kuliah",
    description:
      "Pendampingan dan pengerjaan tugas, khususnya bidang informatika dan komputer.",
  },
  {
    title: "Analisis, Pengolahan & Pemetaan Data",
    description:
      "Pengolahan data menjadi wawasan, termasuk visualisasi dan pemetaan yang informatif.",
  },
  {
    title: "Tools Cyber Security",
    description:
      "Solusi keamanan siber berupa perangkat keras maupun perangkat lunak.",
  },
  {
    title: "Design, Editing Video & Foto",
    description:
      "Jasa desain, editing video, dan foto dengan hasil yang profesional.",
  },
  {
    title: "IoT Projects",
    description:
      "Perancangan dan pengembangan proyek Internet of Things dari konsep hingga implementasi.",
  },
] as const;

export default function LayananPage() {
  return (
    <main className="mx-auto flex w-full max-w-4xl flex-1 flex-col px-4 py-16 sm:px-6 lg:px-8">
      <h1 className="font-heading text-3xl font-bold tracking-tight sm:text-4xl">
        Layanan
      </h1>
      <p className="mt-4 max-w-2xl text-muted-foreground">
        Arkana menawarkan solusi aplikasi dan layanan teknologi untuk membantu
        bisnis dan individu mewujudkan ide menjadi nyata.
      </p>

      <div className="mt-10 grid gap-4 sm:grid-cols-2">
        {SERVICES.map((service) => (
          <Card key={service.title}>
            <CardHeader>
              <CardTitle>{service.title}</CardTitle>
              <CardDescription>{service.description}</CardDescription>
            </CardHeader>
          </Card>
        ))}
      </div>
    </main>
  );
}