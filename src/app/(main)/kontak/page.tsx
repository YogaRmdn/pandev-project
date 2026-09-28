import type { Metadata } from "next";
import ContactForm from "./_components/contact-form";

export const metadata: Metadata = {
  title: "Kontak",
  description:
    "Hubungi tim Arkana untuk konsultasi dan mulai proyek Anda bersama kami.",
};

export default function KontakPage() {
  return <ContactForm />;
}
