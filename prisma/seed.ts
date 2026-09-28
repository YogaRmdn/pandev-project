// @ts-nocheck
import { PrismaClient, Role } from "@prisma/client";
import { hashPassword } from "better-auth/crypto";

const prisma = new PrismaClient();

async function main() {
  console.log("Seeding database...");

  const hashedPassword = await hashPassword("password123");

  const user = await prisma.user.create({
    data: {
      fullname: "Ijichi Nijika",
      email: "ijichinijika@yopmail.com",
      emailVerified: true,
      role: Role.ADMIN,
      accounts: {
        create: {
          issuer: "local:credential",
          accountId: "pending-admin",
          providerId: "credential",
          password: hashedPassword,
        },
      },
    },
    include: { accounts: true },
  });

  const user2 = await prisma.user.create({
    data: {
      fullname: "Gotou Hitori",
      email: "gotouhitori@yopmail.com",
      emailVerified: true,
      role: Role.USER,
      accounts: {
        create: {
          issuer: "local:credential",
          accountId: "pending-user1",
          providerId: "credential",
          password: hashedPassword,
        },
      },
    },
    include: { accounts: true },
  });

  const user3 = await prisma.user.create({
    data: {
      fullname: "Yamada Ryou",
      email: "yamadaryou@yopmail.com",
      emailVerified: true,
      role: Role.USER,
      accounts: {
        create: {
          issuer: "local:credential",
          accountId: "pending-user2",
          providerId: "credential",
          password: hashedPassword,
        },
      },
    },
    include: { accounts: true },
  });

  const user4 = await prisma.user.create({
    data: {
      fullname: "Kita Ikuyo",
      email: "kitaikuyo@yopmail.com",
      emailVerified: true,
      role: Role.USER,
      accounts: {
        create: {
          issuer: "local:credential",
          accountId: "pending-user3",
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

  await prisma.account.update({
    where: { id: user2.accounts[0].id },
    data: { accountId: user2.id },
  });
  await prisma.account.update({
    where: { id: user3.accounts[0].id },
    data: { accountId: user3.id },
  });

  await prisma.account.update({
    where: { id: user4.accounts[0].id },
    data: { accountId: user4.id },
  });

  console.log("Created dummy user");
  console.log(`Email: ${user.email}`);
  console.log("Password: password123");
  console.log("Role:", user.role);
  console.log("\n");
  console.log(`Email: ${user2.email}`);
  console.log("Password: password123");
  console.log("Role:", user2.role);
  console.log("\n");
  console.log(`Email: ${user3.email}`);
  console.log("Password: password123");
  console.log("Role:", user3.role);
  console.log("\n");
  console.log(`Email: ${user4.email}`);
  console.log("Password: password123");
  console.log("Role:", user4.role);
}

main()
  .then(async () => {
    await prisma.$disconnect();
    console.log("Seed completed successfully!");
  })
  .catch(async (e) => {
    console.error("Seed failed:", e);
    await prisma.$disconnect();
    process.exit(1);
  });
