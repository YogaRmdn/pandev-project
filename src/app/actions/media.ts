"use server";

import { cloudinary } from "@/lib/cloudinary";

export type UploadResult = {
  success: boolean;
  url?: string;
  publicId?: string;
  error?: string;
};

export type DeleteResult = {
  success: boolean;
  error?: string;
};

const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/webp", "image/gif"];
const MAX_FILE_SIZE = 5 * 1024 * 1024;

function getPublicIdFromUrl(url: string): string | null {
  const matches = url.match(/\/upload\/(?:v\d+\/)?(.+?)\.(jpg|jpeg|png|gif|webp|svg)$/);
  return matches ? matches[1] : null;
}

export async function uploadMedia(
  file: File,
  folder: string = "pandev"
): Promise<UploadResult> {
  try {
    if (!ALLOWED_TYPES.includes(file.type)) {
      return { success: false, error: "Tipe file tidak valid" };
    }

    if (file.size > MAX_FILE_SIZE) {
      return { success: false, error: "Ukuran file maksimal 5 MB" };
    }

    const bytes = await file.arrayBuffer();
    const buffer = Buffer.from(bytes);

    const result = await new Promise<UploadResult>((resolve) => {
      const uploadStream = cloudinary.uploader.upload_stream(
        {
          folder,
          resource_type: "image",
        },
        (error, result) => {
          if (error) {
            resolve({ success: false, error: error.message });
          } else if (result) {
            resolve({
              success: true,
              url: result.secure_url,
              publicId: result.public_id,
            });
          } else {
            resolve({ success: false, error: "Upload gagal" });
          }
        }
      );

      uploadStream.end(buffer);
    });

    return result;
  } catch (error) {
    console.error("Error uploading media:", error);
    return { success: false, error: "Gagal upload file" };
  }
}

export async function deleteMedia(urlOrPublicId: string): Promise<DeleteResult> {
  try {
    let publicId = urlOrPublicId;

    if (urlOrPublicId.startsWith("http")) {
      const extracted = getPublicIdFromUrl(urlOrPublicId);
      if (!extracted) {
        return { success: false, error: "Tidak dapat mengekstrak public ID" };
      }
      publicId = extracted;
    }

    const result = await cloudinary.uploader.destroy(publicId, {
      resource_type: "image",
    });

    if (result.result === "ok" || result.result === "not found") {
      return { success: true };
    }

    return { success: false, error: "Gagal menghapus file" };
  } catch (error) {
    console.error("Error deleting media:", error);
    return { success: false, error: "Gagal menghapus file" };
  }
}

export async function deleteMultipleMedia(
  urlsOrPublicIds: string[]
): Promise<DeleteResult> {
  try {
    const publicIds = urlsOrPublicIds.map((url) => {
      if (url.startsWith("http")) {
        return getPublicIdFromUrl(url) || url;
      }
      return url;
    });

    const result = await cloudinary.api.delete_resources(publicIds, {
      resource_type: "image",
    });

    return { success: true };
  } catch (error) {
    console.error("Error deleting multiple media:", error);
    return { success: false, error: "Gagal menghapus file" };
  }
}
