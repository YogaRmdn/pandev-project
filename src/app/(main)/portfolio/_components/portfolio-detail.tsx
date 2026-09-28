"use client";

import { useState } from "react";
import Image from "next/image";
import { Button } from "@/components/ui/button";
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  CodeIcon,
  ExternalLinkIcon,
  ImageIcon,
} from "lucide-react";
import { cn } from "@/lib/utils";
import type { Portfolio } from "@/app/dashboard/portfolio/_types/portfolio";
import Link from "next/link";
import { Card, CardContent, CardHeader } from "@/components/ui/card";

interface PropTypes {
  portfolio: Portfolio;
}

export default function PortfolioDetail(props: PropTypes) {
  const { portfolio } = props;
  const {
    name,
    description,
    category,
    demo_link,
    repository_link,
    tech_stacks,
    thumbnail,
    galery,
  } = portfolio;

  const allImages =
    galery && galery.length > 0
      ? galery.map((g) => g.image_url).filter(Boolean)
      : thumbnail
        ? [thumbnail]
        : [];
  const hasImages = allImages.length > 0;
  const images = hasImages ? allImages : [];

  const [selectedIndex, setSelectedIndex] = useState(0);
  const [isHovered, setIsHovered] = useState(false);

  const handlePrev = () => {
    setSelectedIndex((prev) => (prev === 0 ? images.length - 1 : prev - 1));
  };

  const handleNext = () => {
    setSelectedIndex((prev) => (prev === images.length - 1 ? 0 : prev + 1));
  };

  const techStacks = Array.isArray(tech_stacks) ? tech_stacks : [];

  return (
    <div className="flex flex-col gap-6 p-4 lg:flex-row">
      {/* Left Column - Carousel */}
      <div className="w-full lg:w-2/3">
        {/* Main Carousel */}
        <div className="relative w-full">
          <div
            className="relative overflow-hidden border rounded-lg aspect-video"
            onMouseEnter={() => setIsHovered(true)}
            onMouseLeave={() => setIsHovered(false)}
          >
            {hasImages ? (
              <Image
                src={images[selectedIndex]}
                alt={`${name} - Image ${selectedIndex + 1}`}
                fill
                className="object-cover"
                priority
              />
            ) : (
              <div className="flex items-center justify-center w-full h-full bg-muted">
                <ImageIcon className="size-16 text-muted-foreground/50" />
              </div>
            )}
            {images.length > 1 && (
              <>
                <Button
                  variant="secondary"
                  size="icon"
                  className={cn(
                    "absolute left-2 top-1/2  rounded-full bg-black/50 text-white hover:bg-black/70 transition-opacity duration-200",
                    isHovered ? "opacity-100" : "opacity-0",
                  )}
                  type="button"
                  onClick={handlePrev}
                >
                  <ChevronLeftIcon className="size-5" />
                </Button>
                <Button
                  variant="secondary"
                  type="button"
                  size="icon"
                  className={cn(
                    "absolute right-2 top-1/2  rounded-full bg-black/50 text-white hover:bg-black/70 transition-opacity duration-200",
                    isHovered ? "opacity-100" : "opacity-0",
                  )}
                  onClick={handleNext}
                >
                  <ChevronRightIcon className="size-5" />
                </Button>
              </>
            )}
          </div>
        </div>

        {/* Thumbnail Navigation - Horizontal scroll */}
        {hasImages && images.length > 1 && (
          <div className="flex gap-2 pb-2 mt-4 overflow-x-auto snap-x snap-mandatory">
            {images.map((image, index) => (
              <button
                key={image}
                onClick={() => setSelectedIndex(index)}
                className={cn(
                  "relative shrink-0 overflow-hidden rounded-lg border-2 transition-all snap-center",
                  "w-24 h-16",
                  selectedIndex === index
                    ? "border-primary ring-2 ring-primary/20"
                    : "border-transparent opacity-60 hover:opacity-100",
                )}
              >
                <Image
                  src={image}
                  alt={`Thumbnail ${index + 1}`}
                  fill
                  className="object-cover"
                />
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Right Column - Portfolio Info */}
      <Card className="w-full lg:w-1/3 h-fit">
        <CardHeader>
          <div>
            <h1 className="text-2xl font-bold">{name}</h1>
            <div className="flex items-center gap-2 mt-2">
              <span className="px-2 py-1 text-sm font-medium rounded-full bg-primary/10 text-primary">
                {category}
              </span>
              
            </div>
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="prose max-w-none">
            <div className="text-sm font-semibold uppercase text-muted-foreground">
              Description
            </div>
            <p className="mt-2 whitespace-pre-wrap">{description}</p>
          </div>

          {techStacks.length > 0 && (
            <div>
              <div className="text-sm font-semibold uppercase text-muted-foreground">
                Tech Stacks
              </div>
              <div className="flex flex-wrap gap-2 mt-2">
                {techStacks.map((tech) => (
                  <span
                    key={tech}
                    className="px-3 py-1 text-sm border rounded-lg bg-secondary text-secondary-foreground"
                  >
                    {tech}
                  </span>
                ))}
              </div>
            </div>
          )}
          <div>
            <div className="text-sm font-semibold uppercase text-muted-foreground">
              LINKS
            </div>
            <div className="flex gap-3 mt-2">
              {demo_link && (
                <Button asChild>
                  <Link
                    href={`https://${demo_link}`}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <ExternalLinkIcon className="mr-2 size-4" />
                    Lihat Demo
                  </Link>
                </Button>
              )}
              {repository_link && (
                <Button variant="outline" asChild>
                  <Link
                    href={`https://${repository_link}`}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <CodeIcon className="mr-2 size-4" />
                    Repository
                  </Link>
                </Button>
              )}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
