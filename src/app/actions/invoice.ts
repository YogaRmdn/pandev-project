"use server";

import { prisma } from "@/lib/prisma";
import { InvoiceStatus } from "@prisma/client";
import { IUpdateInvoiceInput } from "../dashboard/invoice/_types/Invoice";
import { getInvoiceStatus } from "@/lib/utils";

export async function getInvoices() {
  try {
    const transactions = await prisma.invoice.findMany({
      include: {
        invoice_items: true,
      },
      orderBy: { date: "desc" },
    });
    return transactions;
  } catch (error) {
    throw error;
  }
}
export async function getInvoiceById(uuid: string) {
  try {
    const invoice = await prisma.invoice.findUnique({
      where: { id: uuid },
      include: {
        invoice_items: true,
      },
    });
    return invoice;
  } catch (error) {
    throw error;
  }
}

interface ICreateInvoiceInput {
  description: string;
  date?: string;
  status: string;
  invoice_items: {
    name: string;
    quantity: number;
    price: number;
  }[];
}

export async function createInvoice(data: ICreateInvoiceInput) {
  try {
    const invoice = await prisma.invoice.create({
      data: {
        description: data.description,
        date: data.date ? new Date(data.date) : new Date(),
        status: data.status as InvoiceStatus,
        invoice_items: {
          createMany: {
            data: data.invoice_items,
          },
        },
      },
    });
    return invoice;
  } catch (error) {
    throw error;
  }
}

export async function updateInvoice(uuid: string, data: IUpdateInvoiceInput) {
  try {
    await prisma.invoiceItem.deleteMany({
      where: {
        invoiceId: uuid,
      },
    });
    const invoice = await prisma.invoice.update({
      where: { id: uuid },
      data: {
        description: data.description,
        date: data.date ? new Date(data.date) : new Date(),
        status: data.status as InvoiceStatus,
        invoice_items: {
          createMany: {
            data: data.invoice_items,
          },
        },
      },
    });
    return invoice;
  } catch (error) {
    throw error;
  }
}

export async function updateInvoiceStatus(
  uuid: string,
  status: "PARTIALLY_PAID" | "PAID",
) {
  try {
    const invoice = await getInvoiceById(uuid);
    if (!invoice) throw new Error("Tagihan tidak ditemukan");
    const res = await prisma.invoice.update({
      where: { id: invoice.id },
      data: {
        status,
      },
    });
    let totalAmount = 0;
    invoice.invoice_items.map(
      (item) => (totalAmount += item.price * item.quantity),
    );
    await prisma.transaction.create({
      data: {
        amount: status === "PAID" ? totalAmount : totalAmount * 0.5,
        description: `${invoice.description} - ${getInvoiceStatus(res.status)}`,
        type: "INCOME",
      },
    });
    return invoice;
  } catch (error) {
    throw error;
  }
}

export async function deleteInvoice(uuid: string) {
  try {
    const result = await prisma.invoice.delete({
      where: { id: uuid },
    });
    return result;
  } catch (error) {
    throw error;
  }
}
