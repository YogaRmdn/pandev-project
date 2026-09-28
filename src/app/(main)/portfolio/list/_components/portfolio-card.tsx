"use client";

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { ClockIcon, ExternalLinkIcon, ImageIcon } from "lucide-react";
import Image from "next/image";
import Link from "next/link";

interface Portfolio {
  id: string;
  thumbnail?: string | null;
  name: string;
  category: string;
  description: string;
  tech_stacks: unknown;
  updated_at: Date;
}

function getRelativeTime(date: Date): string {
  const now = new Date();
  const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

  if (diffInSeconds < 60) return "Baru saja";
  if (diffInSeconds < 3600)
    return `${Math.floor(diffInSeconds / 60)} menit yang lalu`;
  if (diffInSeconds < 86400)
    return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`;
  if (diffInSeconds < 604800)
    return `${Math.floor(diffInSeconds / 86400)} hari yang lalu`;
  if (diffInSeconds < 2592000)
    return `${Math.floor(diffInSeconds / 604800)} minggu yang lalu`;
  if (diffInSeconds < 31536000)
    return `${Math.floor(diffInSeconds / 2592000)} bulan yang lalu`;
  return `${Math.floor(diffInSeconds / 31536000)} tahun yang lalu`;
}

interface PropTypes {
  portfolio: Portfolio;
}

export default function PortfolioCard({ portfolio }: PropTypes) {
  const {
    id,
    thumbnail,
    name,
    category,
    description,
    tech_stacks,
    updated_at,
  } = portfolio;
  const techStacks = Array.isArray(tech_stacks)
    ? (tech_stacks as string[])
    : [];

  return (
    <Link href={`/portfolio/${id}`}>
      <Card className="hover:scale-[102%] transition-all">
        <CardHeader>
          <div className="object-cover aspect-video">
            {thumbnail ? (
              <Image
                className="w-full rounded-tl-lg rounded-tr-lg"
                src={thumbnail}
                width={300}
                height={300}
                alt={name}
              />
            ) : (
              <div className="flex items-center justify-center w-full h-full bg-muted rounded-tl-lg rounded-tr-lg">
                <ImageIcon className="size-10 text-muted-foreground/50" />
              </div>
            )}
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex items-center justify-between pb-2 font-semibold border-b">
              <div className="truncate">{name}</div>
            </div>
            <div className="flex items-center gap-1">
              <ClockIcon className="size-4" />{" "}
              <span className="text-sm">
                {getRelativeTime(new Date(updated_at))}
              </span>
            </div>
            <div className="space-y-1">
              <div className="uppercase text-muted-foreground">Kategori</div>
              <div className="px-2 py-1 text-sm border rounded-lg w-fit bg-cyan-200 text-cyan-800">
                {category}
              </div>
            </div>
            <div className="space-y-1">
              <div className="text-sm text-muted-foreground line-clamp-2">
                {description}
              </div>
            </div>
            <div className="space-y-1">
              <div className="uppercase text-muted-foreground">Tech Stacks</div>
              <div className="flex flex-wrap gap-1">
                {techStacks.length > 0 ? (
                  techStacks.slice(0, 3).map((tech) => (
                    <div
                      key={tech}
                      className="px-2 py-1 text-sm border rounded-lg w-fit bg-slate-200 text-slate-800"
                    >
                      {tech}
                    </div>
                  ))
                ) : (
                  <div className="text-sm text-muted-foreground">
                    Tidak ada tech stack
                  </div>
                )}
                {techStacks.length > 3 && (
                  <div className="px-2 py-1 text-sm border rounded-lg w-fit text-slate-800">
                    +{techStacks.length - 3}
                  </div>
                )}
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </Link>
  );
}
