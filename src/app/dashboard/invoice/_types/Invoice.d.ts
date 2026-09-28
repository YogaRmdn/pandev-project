import { Invoice, InvoiceItem } from "@prisma/client";

interface IInvoiceExtended extends Invoice {
  invoice_items: InvoiceItem[];
}

interface IUpdateInvoiceInput {
  description: string;
  date: string;
  status: string;
  invoice_items: {
    name: string;
    quantity: number;
    price: number;
  }[];
}

export type { IInvoiceExtended, IUpdateInvoiceInput };
