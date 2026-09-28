"use server";

import { mkdir, writeFile, unlink } from "fs/promises";
import { join } from "path";
import { randomUUID } from "crypto";

const UPLOAD_DIR = join(process.cwd(), "public", "uploads");

async function ensureUploadDir() {
  try {
    await mkdir(UPLOAD_DIR, { recursive: true });
  } catch {
    // Directory already exists
  }
}

function getUniqueFilename(originalFilename: string): string {
  const ext = originalFilename.split(".").pop() || "";
  const uniqueName = randomUUID();
  return `${uniqueName}.${ext}`;
}

export async function uploadFile(formData: FormData) {
  await ensureUploadDir();

  const file = formData.get("file") as File | null;
  if (!file) {
    return { success: false, error: "No file provided" };
  }

  const allowedTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
  if (!allowedTypes.includes(file.type)) {
    return { success: false, error: "Invalid file type" };
  }

  const uniqueFilename = getUniqueFilename(file.name);
  const filePath = join(UPLOAD_DIR, uniqueFilename);

  const bytes = await file.arrayBuffer();
  await writeFile(filePath, Buffer.from(bytes));

  return {
    success: true,
    url: `/uploads/${uniqueFilename}`,
    filename: uniqueFilename,
  };
}

export async function uploadMultipleFiles(formData: FormData) {
  await ensureUploadDir();

  const files = formData.getAll("files") as File[];
  if (!files.length) {
    return { success: false, error: "No files provided" };
  }

  const allowedTypes = ["image/jpeg", "image/png", "image/webp"];
  const results: { url: string; filename: string }[] = [];
  const errors: string[] = [];

  for (const file of files) {
    if (!allowedTypes.includes(file.type)) {
      errors.push(`${file.name}: invalid file type`);
      continue;
    }

    const uniqueFilename = getUniqueFilename(file.name);
    const filePath = join(UPLOAD_DIR, uniqueFilename);

    const bytes = await file.arrayBuffer();
    await writeFile(filePath, Buffer.from(bytes));

    results.push({
      url: `/uploads/${uniqueFilename}`,
      filename: uniqueFilename,
    });
  }

  return {
    success: errors.length === 0,
    uploaded: results,
    errors,
  };
}

export async function deleteFile(filename: string) {
  const filePath = join(UPLOAD_DIR, filename);

  try {
    await unlink(filePath);
    return { success: true };
  } catch {
    return { success: false, error: "File not found" };
  }
}

export async function deleteMultipleFiles(filenames: string[]) {
  const results: { filename: string; success: boolean; error?: string }[] = [];

  for (const filename of filenames) {
    const filePath = join(UPLOAD_DIR, filename);

    try {
      await unlink(filePath);
      results.push({ filename, success: true });
    } catch {
      results.push({ filename, success: false, error: "File not found" });
    }
  }

  return {
    success: results.every((r) => r.success),
    results,
  };
}
