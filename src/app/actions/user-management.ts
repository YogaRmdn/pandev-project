"use server";

import { getUserSession } from "@/lib/auth-server";
import { prisma } from "@/lib/prisma";
import { hashPassword } from "better-auth/crypto";

export async function getUsers() {
  const session = await getUserSession();
  if (!session) throw new Error("Unauthorized");
  try {
    const users = await prisma.user.findMany({
      select: {
        id: true,
        fullname: true,
        email: true,
        role: true,
        image: true,
      },
      where: {
        id: {
          not: session.user.id,
        },
      },
    });
    return users;
  } catch (error) {
    throw error;
  }
}

export async function deleteUser(id: string) {
  try {
    const res = await prisma.user.delete({
      where: {
        id,
      },
    });
    return res;
  } catch (error) {
    throw error;
  }
}

export async function createUser(data: {
  fullname: string;
  email: string;
  password: string;
  role: "ADMIN" | "USER";
}) {
  try {
    const session = await getUserSession();

    if (!session) {
      throw new Error("Unauthorized");
    }

    if (session.user.role !== "ADMIN") {
      throw new Error("Unauthorized");
    }

    const existingUser = await prisma.user.findUnique({
      where: { email: data.email },
    });

    if (existingUser) {
      throw new Error("Email sudah digunakan");
    }

    const hashedPassword = await hashPassword(data.password);

    const user = await prisma.user.create({
      data: {
        fullname: data.fullname,
        email: data.email,
        role: data.role,
        accounts: {
          create: {
            issuer: "local:credential",
            accountId: "",
            providerId: "credential",
            password: hashedPassword,
          },
        },
      },
      include: { accounts: true },
    });

    await prisma.account.update({
      where: { id: user.accounts[0].id },
      data: { accountId: user.id },
    });

    return user;
  } catch (error) {
    throw error;
  }
}
