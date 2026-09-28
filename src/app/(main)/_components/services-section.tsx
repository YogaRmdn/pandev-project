"use client";

import { motion } from "framer-motion";
import {
  DatabaseIcon,
  GraduationCapIcon,
  HardDriveIcon,
  LockIcon,
  MonitorSmartphoneIcon,
  PaintbrushIcon,
} from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";

const SERVICE_ITEMS = [
  {
    icon: <MonitorSmartphoneIcon className="size-7 md:10" />,
    label: "Web, Mobile, and Desktop App",
    description:
      "Berbagai ide aplikasi Anda, di beragam platform dalam bentuk website, mobile, ataupun desktop.",
  },
  {
    icon: <LockIcon className="size-7 md:10" />,
    label: "Cyber Security Tools",
    description:
      "Aplikasi dan tools untuk pentest dalam bentuk hardware maupun software.",
  },
  {
    icon: <PaintbrushIcon className="size-7 md:10" />,
    label: "Design & Multimedia",
    description:
      "Melayani pembuatan poster, logo, spanduk, dan video editing untuk kebutuhan bisnis Anda.",
  },
  {
    icon: <HardDriveIcon className="size-7 md:10" />,
    label: "IoT Solutions",
    description:
      "Perangkat dan sistem terintegrasi untuk monitoring, kontrol, dan otomasi berbagai kebutuhan",
  },
  {
    icon: <GraduationCapIcon className="size-7 md:10" />,
    label: "Academic Assistant",
    description: "Joki dan bantuan tugas sekolah dan kuliah Anda dengan mudah.",
  },
  {
    icon: <DatabaseIcon className="size-7 md:10" />,
    label: "Data & GIS",
    description: "Jasa analisis, pengolahan, dan pemetaan data.",
  },
];

const containerVariants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1,
    },
  },
};

const itemVariants = {
  hidden: { opacity: 0, y: 30 },
  visible: { opacity: 1, y: 0, transition: { duration: 0.5 } },
};

export default function ServicesSection() {
  return (
    <section
      id="services-section"
      className="w-full p-4 md:p-8 space-y-6"
    >
      <div className="max-w-6xl mx-auto w-full space-y-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5 }}
        >
          <div className="uppercase tracking-wider text-sm sm:text-base text-primary">
            our services
          </div>
          <div className="mb-1 text-2xl md:text-4xl font-bold uppercase text-primary">
            Satu tempat beragam solusi
          </div>
          <div className="text-lg md:text-xl">
            Dari aplikasi hingga kebutuhan lainnya
          </div>
        </motion.div>
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true }}
          className="grid grid-cols-1 gap-4 md:grid-cols-3"
        >
          {SERVICE_ITEMS.map((item) => (
            <motion.div key={`service-item-${item.label}`} variants={itemVariants}>
              <Card className="p-4 h-full">
                <CardContent>
                  <div>
                    <div className="flex items-center justify-center mb-4 text-white rounded-full bg-primary w-12 h-12 md:w-18 md:h-18">
                      {item.icon}
                    </div>
                    <div className="text-base sm:text-lg font-bold">
                      {item.label}
                    </div>
                    <div>{item.description}</div>
                  </div>
                </CardContent>
              </Card>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
}
