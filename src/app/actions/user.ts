"use server";

import { auth } from "@/lib/auth";
import { prisma } from "@/lib/prisma";
import { hashPassword, verifyPassword } from "better-auth/crypto";
import { headers } from "next/headers";

export async function updateUserProfile(data: { fullname: string; email: string }) {
  try {
    const session = await auth.api.getSession({
      headers: await headers(),
    });

    if (!session) {
      return { success: false, error: "Unauthorized" };
    }

    const userId = session.user.id;

    if (data.email !== session.user.email) {
      const existingUser = await prisma.user.findUnique({
        where: { email: data.email },
      });

      if (existingUser && existingUser.id !== userId) {
        return { success: false, error: "Email sudah digunakan oleh pengguna lain" };
      }
    }

    const updatedUser = await prisma.user.update({
      where: { id: userId },
      data: {
        fullname: data.fullname,
        email: data.email,
      },
    });
    return updatedUser;
  } catch (error) {
    console.error("Error updating user profile:", error);
    throw(error)
  }
}

export async function updatePassword(data: { currentPassword: string; newPassword: string }) {
  try {
    const session = await auth.api.getSession({
      headers: await headers(),
    });

    if (!session) {
      return { success: false, error: "Unauthorized" };
    }

    const account = await prisma.account.findFirst({
      where: {
        userId: session.user.id,
        issuer: "local:credential",
        providerId: "credential",
      },
    });

    if (!account || !account.password) {
      return { success: false, error: "Akun tidak memiliki password" };
    }

    const isCurrentPasswordValid = await verifyPassword({
      password: data.currentPassword,
      hash: account.password,
    });

    if (!isCurrentPasswordValid) {
      return { success: false, error: "Password saat ini salah" };
    }

    const hashedNewPassword = await hashPassword(data.newPassword);

    await prisma.account.update({
      where: { id: account.id },
      data: { password: hashedNewPassword },
    });

    return { success: true };
  } catch (error) {
    console.error("Error updating password:", error);
    return { success: false, error: "Gagal mengupdate password" };
  }
}



export async function updateUserImage(imageUrl: string | null) {
  try {
    const session = await auth.api.getSession({
      headers: await headers(),
    });

    if (!session) {
      return { success: false, error: "Unauthorized" };
    }

    const updatedUser = await prisma.user.update({
      where: { id: session.user.id },
      data: { image: imageUrl },
    });

    return { success: true, data: { image: updatedUser.image } };
  } catch (error) {
    console.error("Error updating user image:", error);
    return { success: false, error: "Gagal mengupdate avatar" };
  }
}
