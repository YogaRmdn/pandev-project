"use client";

import { deletePortfolio } from "@/app/actions/portfolio";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
  CircleDotIcon,
  CircleIcon,
  ClockIcon,
  EllipsisVerticalIcon,
  ExternalLinkIcon,
  ImageIcon,
  PenIcon,
  TrashIcon,
} from "lucide-react";
import Image from "next/image";
import { Portfolio } from "../_types/portfolio";
import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import Link from "next/link";
import { getRelativeTime } from "@/lib/utils";



type PropTypes = Portfolio;

export default function PortfolioItem(props: PropTypes) {
  const { thumbnail, name, category, status, updated_at, tech_stacks, id } =
    props;
  const techStacks = Array.isArray(tech_stacks) ? tech_stacks : [];

  const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
  const [openPopover, setOpenPopover] = useState(false);

  const deleteMutation = useMutation({
    mutationFn: deletePortfolio,
    onSuccess: () => {
      toast.success("Berhasil menghapus portfolio")
      setOpenDeleteDialog(false)
    },
    onError: (error) => {
      toast.error(`Gagal menghapus portofolio`);
      setOpenDeleteDialog(false);
    },
  });

  const handleDelete = () => {
    setOpenPopover(false);
    deleteMutation.mutate(id);
  };

  return (
    <>
      <Card className="hover:scale-[102%] transition-all">
        <CardHeader>
          <div>
            {thumbnail ? (
              <Image
                className="w-full rounded-tl-lg rounded-tr-lg object-cover aspect-video"
                src={thumbnail}
                width={200}
                height={300}
                alt={name}
              />
            ) : (
              <div className="flex items-center justify-center w-full aspect-video bg-muted rounded-tl-lg rounded-tr-lg">
                <ImageIcon className="size-10 text-muted-foreground/50" />
              </div>
            )}
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex items-center justify-between pb-2 font-semibold border-b">
              <div className="truncate">{name}</div>
              <Popover open={openPopover} onOpenChange={setOpenPopover}>
                <PopoverTrigger asChild>
                  <Button variant={"ghost"}>
                    <EllipsisVerticalIcon className="size-4" />
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-fit">
                  <Link href={`/dashboard/portfolio/edit/${id}`}>
                    <Button
                      className="flex justify-start gap-1"
                      type="button"
                      variant={"ghost"}
                    >
                      <PenIcon className="size-4" /> Edit
                    </Button>
                  </Link>
                  <Link href={`/portfolio/${id}`} target="_blank">
                    <Button
                      className="flex justify-start gap-1"
                      type="button"
                      variant={"ghost"}
                    >
                      <ExternalLinkIcon className="size-4" />
                      Preview
                    </Button>
                  </Link>
                  <Button
                    className="flex justify-start gap-1 text-destructive hover:text-destructive"
                    type="button"
                    variant={"ghost"}
                    onClick={() => {
                      setOpenPopover(false);
                      setOpenDeleteDialog(true);
                    }}
                  >
                    <TrashIcon className="size-4" /> Delete
                  </Button>
                </PopoverContent>
              </Popover>
            </div>
            <div className="flex items-center gap-1">
              <ClockIcon className="size-4" />{" "}
              <span className="text-sm">
                {getRelativeTime(new Date(updated_at))}
              </span>
            </div>
            <div className="space-y-1">
              <div className="uppercase text-muted-foreground">
                Status & Category
              </div>
              <div className="flex gap-1">
                {status === "published" ? (
                  <div className="flex items-center gap-1 p-1 text-sm text-green-800 bg-green-200 border rounded-lg w-fit">
                    <CircleDotIcon className="size-4" /> Published
                  </div>
                ) : (
                  <div className="flex items-center gap-1 p-1 text-sm border rounded-lg text-amber-800 bg-amber-200 w-fit">
                    <CircleIcon className="size-4" /> Draft
                  </div>
                )}
                <div className="px-2 py-1 text-sm border rounded-lg w-fit bg-cyan-200 text-cyan-800">
                  {category}
                </div>
              </div>
            </div>
            <div className="space-y-1">
              <div className="uppercase text-muted-foreground">Tech Stacks</div>
              <div className="flex flex-wrap gap-1">
                {techStacks.length > 0 ? (
                  techStacks.slice(0, 2).map((tech) => (
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
                {techStacks.length > 2 && (
                  <div className="px-2 py-1 text-sm border rounded-lg w-fit text-slate-800">
                    +{techStacks.length - 2}
                  </div>
                )}
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <AlertDialog open={openDeleteDialog} onOpenChange={setOpenDeleteDialog}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Hapus Portofolio</AlertDialogTitle>
            <AlertDialogDescription>
              Apakah Anda yakin ingin menghapus portofolio &quot;{name}&quot;?
              Tindakan ini tidak dapat dibatalkan.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={deleteMutation.isPending}>
              Batal
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDelete}
              disabled={deleteMutation.isPending}
              className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            >
              {deleteMutation.isPending ? "Menghapus..." : "Hapus"}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
}
