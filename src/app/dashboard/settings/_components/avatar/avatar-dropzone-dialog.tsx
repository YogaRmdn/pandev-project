"use client";

import { uploadMedia, deleteMedia } from "@/app/actions/media";
import { updateUserImage } from "@/app/actions/user";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Spinner } from "@/components/ui/spinner";
import { cn } from "@/lib/utils";
import { ArrowLeftIcon, UploadIcon } from "lucide-react";
import Image from "next/image";
import { useCallback, useEffect, useRef, useState } from "react";
import Cropper from "react-easy-crop";
import { Area } from "react-easy-crop";
import { toast } from "sonner";

interface PropTypes {
  open: boolean;
  setOpen: (value: boolean) => void;
  currentImageUrl: string | null;
  onSaved: () => void;
}

type Step = "dropzone" | "crop" | "preview";

function getCroppedImg(imageSrc: string, crop: Area): Promise<Blob> {
  return new Promise((resolve, reject) => {
    const image = new window.Image();
    image.crossOrigin = "anonymous";
    image.onload = () => {
      const canvas = document.createElement("canvas");
      canvas.width = crop.width;
      canvas.height = crop.height;
      const ctx = canvas.getContext("2d");
      if (!ctx) {
        reject(new Error("Failed to get canvas context"));
        return;
      }
      ctx.drawImage(
        image,
        crop.x,
        crop.y,
        crop.width,
        crop.height,
        0,
        0,
        crop.width,
        crop.height
      );
      canvas.toBlob((blob) => {
        if (blob) {
          resolve(blob);
        } else {
          reject(new Error("Failed to create blob"));
        }
      }, "image/jpeg", 0.9);
    };
    image.onerror = () => reject(new Error("Failed to load image"));
    image.src = imageSrc;
  });
}

export default function AvatarDropzoneDialog({
  open,
  setOpen,
  currentImageUrl,
  onSaved,
}: PropTypes) {
  const dropzoneInputRef = useRef<HTMLInputElement>(null);
  const [isDragging, setIsDragging] = useState(false);

  const [step, setStep] = useState<Step>("dropzone");
  const [rawFileUrl, setRawFileUrl] = useState<string>("");
  const [crop, setCrop] = useState({ x: 0, y: 0 });
  const [zoom, setZoom] = useState(1);
  const [croppedAreaPixels, setCroppedAreaPixels] = useState<Area | null>(null);
  const [tempUploadedUrl, setTempUploadedUrl] = useState<string>("");
  const [isProcessing, setIsProcessing] = useState(false);

  const isPreviewDirty = tempUploadedUrl !== "" && tempUploadedUrl !== currentImageUrl;

  useEffect(() => {
    if (open && currentImageUrl) {
      setStep("preview");
      setTempUploadedUrl(currentImageUrl);
    } else if (open) {
      setStep("dropzone");
    }
  }, [open, currentImageUrl]);

  const resetState = useCallback(() => {
    setStep("dropzone");
    if (rawFileUrl) URL.revokeObjectURL(rawFileUrl);
    setRawFileUrl("");
    setCrop({ x: 0, y: 0 });
    setZoom(1);
    setCroppedAreaPixels(null);
    setTempUploadedUrl("");
    setIsProcessing(false);
    setIsDragging(false);
  }, [rawFileUrl]);

  const cleanupTempFile = useCallback(async () => {
    if (tempUploadedUrl && tempUploadedUrl !== currentImageUrl) {
      await deleteMedia(tempUploadedUrl);
    }
  }, [tempUploadedUrl, currentImageUrl]);

  const handleClose = useCallback(async () => {
    await cleanupTempFile();
    resetState();
    setOpen(false);
  }, [cleanupTempFile, resetState, setOpen]);

  const handleFile = (file: File) => {
    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
      toast.error("Ukuran file maksimal 2 MB");
      return;
    }

    if (!file.type.startsWith("image/")) {
      toast.error("File harus berupa gambar");
      return;
    }

    if (rawFileUrl) URL.revokeObjectURL(rawFileUrl);
    const url = URL.createObjectURL(file);
    setRawFileUrl(url);
    setStep("crop");
  };

  const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDragging(false);
    const file = e.dataTransfer.files[0];
    if (file) handleFile(file);
  };

  const handleCropComplete = useCallback((_: Area, croppedPixels: Area) => {
    setCroppedAreaPixels(croppedPixels);
  }, []);

  const handleCropAndUpload = async () => {
    if (!rawFileUrl || !croppedAreaPixels) return;

    setIsProcessing(true);
    try {
      const croppedBlob = await getCroppedImg(rawFileUrl, croppedAreaPixels);
      const croppedFile = new File([croppedBlob], "avatar.jpg", {
        type: "image/jpeg",
      });

      const result = await uploadMedia(croppedFile, "pandev/avatars");

      if (result.success && result.url) {
        setTempUploadedUrl(result.url);
        setStep("preview");
      } else {
        toast.error(result.error || "Gagal upload gambar");
      }
    } catch {
      toast.error("Gagal memproses gambar");
    } finally {
      setIsProcessing(false);
    }
  };

  const handleSave = async () => {
    if (!tempUploadedUrl || tempUploadedUrl === currentImageUrl) return;

    setIsProcessing(true);
    try {
      if (currentImageUrl) {
        await deleteMedia(currentImageUrl);
      }

      const result = await updateUserImage(tempUploadedUrl);
      if (result.success) {
        toast.success("Avatar berhasil disimpan");
        setTempUploadedUrl("");
        resetState();
        setOpen(false);
        onSaved();
      } else {
        toast.error(result.error || "Gagal menyimpan avatar");
      }
    } catch {
      toast.error("Gagal menyimpan avatar");
    } finally {
      setIsProcessing(false);
    }
  };

  const handleGantiFoto = async () => {
    if (tempUploadedUrl && tempUploadedUrl !== currentImageUrl) {
      await deleteMedia(tempUploadedUrl);
    }
    setTempUploadedUrl("");
    setStep("dropzone");
  };

  return (
    <Dialog
      open={open}
      onOpenChange={(value) => {
        if (!value) {
          handleClose();
        }
      }}
    >
      <DialogContent
        className="sm:max-w-md"
        showCloseButton={true}
        onPointerDownOutside={(e) => e.preventDefault()}
        onInteractOutside={(e) => e.preventDefault()}
      >
        <DialogHeader>
          <DialogTitle>Ubah Avatar</DialogTitle>
        </DialogHeader>

        {step === "dropzone" && (
          <div
            className={cn(
              "flex border-2 border-dotted cursor-pointer border-black/30 min-h-60 text-muted-foreground transition-colors",
              isDragging && "bg-primary/20 border-primary"
            )}
          >
            <input
              ref={dropzoneInputRef}
              className="hidden"
              type="file"
              accept="image/*"
              onChange={(e) => {
                const file = e.target.files?.[0] ?? null;
                if (file) handleFile(file);
                e.target.value = "";
              }}
            />
            <div
              className="flex flex-col justify-center items-center flex-1 gap-2"
              onDragOver={(e) => {
                e.preventDefault();
                setIsDragging(true);
              }}
              onDragLeave={(e) => {
                e.preventDefault();
                setIsDragging(false);
              }}
              onClick={() => dropzoneInputRef.current?.click()}
              onDrop={handleDrop}
            >
              <UploadIcon className="size-8" />
              <div className="text-center">
                <div>Upload Gambar</div>
                <div className="text-xs">JPG, PNG, WebP (Maks 2 MB)</div>
              </div>
            </div>
          </div>
        )}

        {step === "crop" && rawFileUrl && (
          <div className="flex flex-col gap-4">
            <div className="relative w-full aspect-square bg-black/5 rounded-lg overflow-hidden">
              <Cropper
                image={rawFileUrl}
                crop={crop}
                zoom={zoom}
                aspect={1}
                onCropChange={setCrop}
                onZoomChange={setZoom}
                onCropComplete={handleCropComplete}
              />
            </div>
            <div className="flex flex-col gap-2">
              <label className="text-sm text-muted-foreground">
                Zoom: {zoom.toFixed(1)}x
              </label>
              <input
                type="range"
                min={1}
                max={3}
                step={0.1}
                value={zoom}
                onChange={(e) => setZoom(Number(e.target.value))}
                className="w-full"
              />
            </div>
            <div className="flex gap-2">
              <Button
                type="button"
                variant="outline"
                onClick={handleGantiFoto}
                disabled={isProcessing}
              >
                <ArrowLeftIcon className="size-4 mr-1" />
                Kembali
              </Button>
              <Button
                type="button"
                onClick={handleCropAndUpload}
                disabled={isProcessing}
                className="flex-1"
              >
                {isProcessing ? (
                  <>
                    <Spinner className="size-4 mr-2" />
                    Memproses...
                  </>
                ) : (
                  "Crop & Upload"
                )}
              </Button>
            </div>
          </div>
        )}

        {step === "preview" && tempUploadedUrl && (
          <div className="flex flex-col gap-4">
            <div className="flex justify-center">
              <div className="relative w-40 h-40 rounded-full overflow-hidden border-2 border-border">
                <Image
                  src={tempUploadedUrl}
                  alt="avatar preview"
                  fill
                  className="object-cover"
                />
              </div>
            </div>
            <div className="flex flex-col gap-2">
              <Button
                type="button"
                onClick={handleSave}
                disabled={isProcessing || !isPreviewDirty}
                className="w-full"
              >
                {isProcessing ? (
                  <>
                    <Spinner className="size-4 mr-2" />
                    Menyimpan...
                  </>
                ) : (
                  "Simpan Avatar"
                )}
              </Button>
              <Button
                type="button"
                variant="outline"
                onClick={handleGantiFoto}
                disabled={isProcessing}
                className="w-full"
              >
                Ganti Foto
              </Button>
            </div>
          </div>
        )}
      </DialogContent>
    </Dialog>
  );
}
