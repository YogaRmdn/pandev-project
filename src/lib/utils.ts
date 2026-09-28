import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function convertToIDR(value: number) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(value);
}

export function getInvoiceStatus(status: string) {
  switch (status) {
    case "PAID":
      return "Lunas";
    case "PARTIALLY_PAID":
      return "Sebagian dibayar / DP";
    default:
      return "Belum dibayar";
  }
}

export function getRelativeTime(date: Date): string {
  const now = new Date();
  const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);
  if (diffInSeconds < 60) return "Baru saja";
  if (diffInSeconds < 3600)
    return `Diupdate ${Math.floor(diffInSeconds / 60)} menit yang lalu`;
  if (diffInSeconds < 86400)
    return `Diupdate ${Math.floor(diffInSeconds / 3600)} jam yang lalu`;
  if (diffInSeconds < 604800)
    return `Diupdate ${Math.floor(diffInSeconds / 86400)} hari yang lalu`;
  if (diffInSeconds < 2592000)
    return `Diupdate ${Math.floor(diffInSeconds / 604800)} minggu yang lalu`;
  if (diffInSeconds < 31536000)
    return `Diupdate ${Math.floor(diffInSeconds / 2592000)} bulan yang lalu`;
  return `Diupdate ${Math.floor(diffInSeconds / 31536000)} tahun yang lalu`;
}
