"use server";

import { prisma } from "@/lib/prisma";
import { deleteMultipleMedia } from "./media";
import { auth } from "@/lib/auth";
import { headers } from "next/headers";

export async function getPortfolioByUuid(uuid: string) {
  const session = await auth.api.getSession({
    headers: await headers(),
  });
  if (!session) {
    throw new Error("Terjadi kesalahan, coba beberapa saat lagi.");
  }
  const user = session.user;
  try {
    const portfolio = await prisma.portfolio.findUnique({
      where: { id: uuid, created_by: user.id },
      include: { galery: true },
    });

    if (!portfolio) {
      throw new Error("Portfolio tidak ditemukan");
    }

    return portfolio;
  } catch (error) {
    throw error;
  }
}

export async function deletePortfolio(uuid: string) {
  const session = await auth.api.getSession({
    headers: await headers(),
  });
  if (!session) {
    throw new Error("Gagal menghapus portfolio");
  }
  const user = session.user;
  try {
    const portfolio = await prisma.portfolio.findUnique({
      where: { id: uuid, created_by: user.id },
      include: { galery: true },
    });

    if (!portfolio) {
      return { success: false, error: "Portofolio tidak ditemukan" };
    }

    const filesToDelete: string[] = [];

    if (portfolio.thumbnail) {
      filesToDelete.push(portfolio.thumbnail);
    }

    for (const image of portfolio.galery) {
      filesToDelete.push(image.image_url);
    }

    if (filesToDelete.length > 0) {
      await deleteMultipleMedia(filesToDelete);
    }

    await prisma.portfolio.delete({
      where: { id: uuid },
    });
    return portfolio;
  } catch (error) {
    console.error("Error deleting portfolio:", error);
    throw error;
  }
}

export async function getPortfolios() {
  const session = await auth.api.getSession({
    headers: await headers(),
  });
  if (!session) {
    throw new Error("Terjadi kesalahan");
  }
  const user = session.user;

  try {
    const portfolios = await prisma.portfolio.findMany({
      orderBy: { created_at: "desc" },
      where: { created_by: user.id },
      include: { galery: true },
    });
    return portfolios;
  } catch (error) {
    console.error("Error fetching portfolios:", error);
    throw error;
  }
}

interface CreatePortfolioInput {
  thumbnail?: string;
  name: string;
  category: string;
  description: string;
  demo_link?: string;
  repository_link?: string;
  status?: string;
  tech_stacks?: string[];
  galery?: string[];
}

export async function createPortfolio(data: CreatePortfolioInput) {
  const session = await auth.api.getSession({
    headers: await headers(), // you need to pass the headers object.
  });
  if (!session) {
    throw new Error("Terjadi kesalahn");
  }
  const user = session.user;
  try {
    const portfolio = await prisma.portfolio.create({
      data: {
        thumbnail: data.thumbnail || "",
        name: data.name,
        category: data.category,
        description: data.description,
        demo_link: data.demo_link || null,
        repository_link: data.repository_link || "",
        status: data.status || "draft",
        tech_stacks: data.tech_stacks || [],
        galery: {
          create: (data.galery || []).map((url) => ({
            image_url: url,
          })),
        },
        created_by: user.id,
      },
      include: {
        galery: true,
      },
    });
    return portfolio;
  } catch (error) {
    throw error;
  }
}

interface UpdatePortfolioInput {
  uuid: string;
  thumbnail?: string;
  name: string;
  category: string;
  description: string;
  demo_link?: string;
  repository_link?: string;
  status?: string;
  tech_stacks?: string[];
  galery?: string[];
}

export async function updatePortfolio(data: UpdatePortfolioInput) {
  const session = await auth.api.getSession({
    headers: await headers(),
  });
  if (!session) {
    throw new Error("Terjadi kesalahan");
  }
  const user = session.user;
  try {
    const existing = await prisma.portfolio.findUnique({
      where: { id: data.uuid },
      include: { galery: true },
    });

    if (!existing) {
      throw new Error("Portfolio tidak ditemukan");
    }

    if (data.thumbnail !== existing.thumbnail) {
      await deleteMultipleMedia([existing.thumbnail]);
    }

    const existingGalleryUrls = existing.galery.map((g) => g.image_url);
    const removedGalleryUrls = existingGalleryUrls.filter(
      (url) => !(data.galery || []).includes(url),
    );

    if (removedGalleryUrls.length > 0) {
      await deleteMultipleMedia(removedGalleryUrls);
    }

    await prisma.portfolioGalery.deleteMany({
      where: { portfolio_id: data.uuid },
    });

    const portfolio = await prisma.portfolio.update({
      where: { id: data.uuid, created_by: user.id },
      data: {
        thumbnail: data.thumbnail || "",
        name: data.name,
        category: data.category,
        description: data.description,
        demo_link: data.demo_link || null,
        repository_link: data.repository_link || "",
        status: data.status || "draft",
        tech_stacks: data.tech_stacks || [],
        galery: {
          create: (data.galery || []).map((url) => ({
            image_url: url,
          })),
        },
      },
      include: { galery: true },
    });

    return portfolio;
  } catch (error) {
    throw error;
  }
}
