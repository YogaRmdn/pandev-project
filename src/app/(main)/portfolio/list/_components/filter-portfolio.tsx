"use client";

import { useRouter, useSearchParams } from "next/navigation";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";
import { Toggle } from "@/components/ui/toggle";
import { FilterIcon } from "lucide-react";

const PORTFOLIO_CATEGORIES = [
  "Web App",
  "Mobile App",
  "Desktop App",
  "Multiplatform App",
  "Cyber Security Tools",
  "Design & Multimedia",
  "IoT Solutions",
  "Data & GIS",
] as const;

export default function FilterPortfolio() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [open, setOpen] = useState(false);

  const [draftCategories, setDraftCategories] = useState<string[]>([]);

  const currentCategories =
    searchParams.get("category")?.split(",").filter(Boolean) || [];

  const handleOpenChange = (nextOpen: boolean) => {
    if (nextOpen) {
      setDraftCategories([...currentCategories]);
    }
    setOpen(nextOpen);
  };

  const toggleDraftCategory = (category: string) => {
    setDraftCategories((prev) =>
      prev.includes(category)
        ? prev.filter((v) => v !== category)
        : [...prev, category],
    );
  };

  const handleApply = () => {
    const params = new URLSearchParams(searchParams.toString());

    if (draftCategories.length > 0) {
      params.set("category", draftCategories.join(","));
    } else {
      params.delete("category");
    }

    params.delete("page");
    router.push(`?${params.toString()}`);
    setOpen(false);
  };

  const hasActiveFilters = currentCategories.length > 0;

  return (
    <Sheet open={open} onOpenChange={handleOpenChange}>
      <SheetTrigger asChild>
        <Button variant={hasActiveFilters ? "default" : "outline"}>
          <FilterIcon className="size-4" /> Filter
          {hasActiveFilters && (
            <span className="ml-1 size-5 rounded-full bg-primary-foreground text-primary text-xs flex items-center justify-center">
              {currentCategories.length}
            </span>
          )}
        </Button>
      </SheetTrigger>
      <SheetContent>
        <SheetHeader>
          <SheetTitle className="font-bold text-primary">
            Filter Portfolio
          </SheetTitle>
          <SheetDescription>
            Pilih kategori untuk mengfilter portfolio.
          </SheetDescription>
        </SheetHeader>
        <div className="space-y-6 p-4">
          <div className="space-y-3">
            <h3 className="text-sm font-medium">Kategori</h3>
            <div className="flex flex-wrap gap-2">
              {PORTFOLIO_CATEGORIES.map((category) => (
                <Toggle
                  key={category}
                  variant="outline"
                  size="sm"
                  pressed={draftCategories.includes(category)}
                  onPressedChange={() => toggleDraftCategory(category)}
                >
                  {category}
                </Toggle>
              ))}
            </div>
          </div>
        </div>
        <SheetFooter>
          <Button variant="outline" onClick={() => setOpen(false)}>
            Batal
          </Button>
          <Button onClick={handleApply}>Terapkan filter</Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
