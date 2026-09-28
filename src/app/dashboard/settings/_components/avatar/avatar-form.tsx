"use client";

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
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
import { deleteMedia } from "@/app/actions/media";
import { updateUserImage } from "@/app/actions/user";
import { PenIcon, TrashIcon, UserIcon } from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
import AvatarDropzoneDialog from "./avatar-dropzone-dialog";

interface PropTypes {
  userImageUrl?: string | null;
  refetchSession: () => void;
}

export default function AvatarForm({ userImageUrl, refetchSession }: PropTypes) {
  const [open, setOpen] = useState(false);
  const [deleteAlertOpen, setDeleteAlertOpen] = useState(false);

  const handleDelete = async () => {
    if (!userImageUrl) return;

    const deleteResult = await deleteMedia(userImageUrl);
    if (!deleteResult.success) {
      toast.error("Gagal menghapus file avatar");
      return;
    }

    const updateResult = await updateUserImage(null);
    if (!updateResult.success) {
      toast.error(updateResult.error || "Gagal mengupdate avatar");
      return;
    }

    toast.success("Avatar berhasil dihapus");
    refetchSession();
  };

  const handleEditClick = () => {
    setOpen(true);
  };

  return (
    <>
      <div className="py-4 px-8 mx-4 border-b flex items-center">
        <div className="w-1/2">
          <div className="font-bold">Avatar</div>
          <div>Edit avatar Anda</div>
        </div>
        <div className="w-full">
          <div className="relative w-fit">
            <Avatar className="size-24">
              {userImageUrl && <AvatarImage src={userImageUrl} alt="avatar" />}
              <AvatarFallback>
                <UserIcon className="size-10" />
              </AvatarFallback>
            </Avatar>
            <Button
              onClick={handleEditClick}
              variant="outline"
              className="rounded-full absolute top-0.5 -right-4"
              size="icon"
            >
              <PenIcon className="size-4" />
            </Button>
            {userImageUrl && (
              <Button
                onClick={() => setDeleteAlertOpen(true)}
                variant="outline"
                className="rounded-full absolute -bottom-0.5 -right-4 text-destructive hover:text-destructive"
                size="icon"
              >
                <TrashIcon className="size-4" />
              </Button>
            )}
          </div>
        </div>
      </div>

      <AlertDialog open={deleteAlertOpen} onOpenChange={setDeleteAlertOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Hapus Avatar</AlertDialogTitle>
            <AlertDialogDescription>
              Apakah Anda yakin ingin menghapus avatar? Tindakan ini tidak dapat dibatalkan.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Batal</AlertDialogCancel>
            <AlertDialogAction
              variant="destructive"
              onClick={handleDelete}
            >
              Hapus
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      <AvatarDropzoneDialog
        open={open}
        setOpen={setOpen}
        currentImageUrl={userImageUrl ?? null}
        onSaved={() => {
         refetchSession();
        }}
      />
    </>
  );
}
