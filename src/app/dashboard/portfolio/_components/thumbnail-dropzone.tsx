"use client";

import { uploadMedia, deleteMedia } from "@/app/actions/media";
import { Button } from "@/components/ui/button";
import { Spinner } from "@/components/ui/spinner";
import { cn } from "@/lib/utils";
import { UploadIcon, XIcon } from "lucide-react";
import Image from "next/image";
import { DragEvent, useRef, useState } from "react";
import { toast } from "sonner";

interface PropTypes {
  value?: string;
  onChange: (url: string) => void;
  isinvalid: string;
}

export default function ThumbnailDropzone(props: PropTypes) {
  const { onChange, value, isinvalid } = props;
  const dropzoneInputRef = useRef<HTMLInputElement>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [isUploading, setIsUploading] = useState(false);

  const MAX_FILE_SIZE = 5 * 1024 * 1024;

  const handleUpload = async (file: File) => {
    if (file.size > MAX_FILE_SIZE) {
      toast.error("Ukuran file maksimal 5MB");
      return;
    }
    setIsUploading(true);
    try {
      const result = await uploadMedia(file, "pandev/portfolio/thumbnails");

      if (result.success && result.url) {
        onChange(result.url);
      } else {
        toast.error(result.error || "Gagal upload thumbnail");
      }
    } catch {
      toast.error("Gagal upload thumbnail");
    } finally {
      setIsUploading(false);
    }
  };

  const handleDelete = async () => {
    if (!value) return;

    await deleteMedia(value);
    onChange("");
  };

  const handleFileChange = (selectedFile: File | null) => {
    if (!selectedFile) return;
    handleUpload(selectedFile);
  };

  const handleDropzone = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    setIsDragging(false);
    if (file) handleFileChange(file);
  };

  return (
    <div
      className={cn(
        "flex border-2 border-dotted cursor-pointer border-black/30 min-h-60 text-muted-foreground",
        isinvalid === "true" && "border-destructive",
        isDragging && "bg-primary/20 scale-[102%]",
      )}
    >
      <input
        ref={dropzoneInputRef}
        className="hidden"
        type="file"
        accept="image/*"
        onChange={(e) => {
          const selectedFile = e.target.files?.[0] ?? null;
          handleFileChange(selectedFile);
        }}
      />
      {isUploading ? (
        <div className="flex flex-col justify-center items-center flex-1 gap-2">
          <Spinner />
          <div>Mengupload...</div>
        </div>
      ) : value ? (
        <div className="relative m-4 mx-auto">
          <Image
            width={500}
            height={300}
            src={value}
            alt="thumbnail image"
            className="object-cover aspect-video"
          />
          <Button
            type="button"
            className="absolute -top-3 -right-3"
            size={"icon-lg"}
            onClick={handleDelete}
          >
            <XIcon />
          </Button>
        </div>
      ) : (
        <div
          className={"flex flex-col justify-center items-center flex-1 gap-2"}
          onDragOver={(e) => {
            e.preventDefault();
            setIsDragging(true);
          }}
          onDragLeave={(e) => {
            e.preventDefault();
            setIsDragging(false);
          }}
          onClick={() => {
            dropzoneInputRef.current?.click();
          }}
          onDrop={handleDropzone}
        >
          <UploadIcon className="size-8" />
          <div className="text-center">
            <div>Upload Thumbnail</div>
            <div>Image: jpg/png/webp</div>
          </div>
        </div>
      )}
    </div>
  );
}
