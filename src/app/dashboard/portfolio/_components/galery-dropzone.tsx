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
  value: string[];
  onChange: (urls: string[]) => void;
}

const allowedTypes = ["image/jpeg", "image/png", "image/webp"];
const MAX_FILE_SIZE = 5 * 1024 * 1024;

export default function GaleryDropzone(props: PropTypes) {
  const { onChange, value = [] } = props;

  const dropzoneInputRef = useRef<HTMLInputElement>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [uploadingIndexes, setUploadingIndexes] = useState<number[]>([]);

  const handleUpload = async (file: File) => {
    if (!allowedTypes.includes(file.type)) {
      toast.error("Format file harus JPG, PNG, atau WEBP");
      return;
    }
    if (file.size > MAX_FILE_SIZE) {
      toast.error("Ukuran file maksimal 5MB");
      return;
    }

    const tempIndex = value.length;
    setUploadingIndexes((prev) => [...prev, tempIndex]);

    try {
      const result = await uploadMedia(file, "pandev/portfolio/gallery");

      if (result.success && result.url) {
        onChange([...value, result.url]);
      } else {
        toast.error(result.error || "Gagal upload gambar");
      }
    } catch {
      toast.error("Gagal upload gambar");
    } finally {
      setUploadingIndexes((prev) => prev.filter((i) => i !== tempIndex));
    }
  };

  const handleDeleteMedia = async (index: number) => {
    const url = value[index];
    if (url) {
      await deleteMedia(url);
    }
    onChange(value.filter((_, i) => i !== index));
  };

  const handleDropzone = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    setIsDragging(false);
    if (file) handleUpload(file);
  };

  return (
    <div>
      <div
        className={cn(
          "flex border-2 border-dotted cursor-pointer border-black/30 min-h-60 text-muted-foreground",
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
            if (selectedFile) handleUpload(selectedFile);
          }}
        />
        <div
          className="flex flex-col justify-center items-center flex-1 gap-2 border"
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
            <div>Upload Foto Galeri</div>
            <div>Image: jpg/png/webp</div>
          </div>
        </div>
      </div>
      <div className="grid grid-cols-3 mt-4">
        {value.map((url, index) => (
          <div key={url} className="relative">
            <Image
              width={200}
              height={200}
              src={url}
              alt={`gallery image ${index + 1}`}
              className="object-cover aspect-square"
            />
            <Button
              type="button"
              className="absolute -top-1 -right-1"
              size={"icon-lg"}
              onClick={() => handleDeleteMedia(index)}
            >
              <XIcon />
            </Button>
          </div>
        ))}
        {uploadingIndexes.map((index) => (
          <div
            key={`uploading-${index}`}
            className="relative flex items-center justify-center aspect-square bg-muted"
          >
            <Spinner />
          </div>
        ))}
      </div>
    </div>
  );
}
