"use client";

import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { useQuery } from "@tanstack/react-query";
import { FilterIcon, PlusIcon } from "lucide-react";
import { useState } from "react";
import { Fragment } from "react/jsx-runtime";
import InvoiceTable from "./invoice-table";
import CreateInvoiceDialog from "./create-invoice-dialog";
import { getInvoices } from "@/app/actions/invoice";
import { IInvoiceExtended } from "../_types/Invoice";

export default function InvoiceContent() {
  const {
    isPending,
    data: invoices,
    refetch,
  } = useQuery({
    queryFn: getInvoices,
    queryKey: ["invoices"],
  });
  const [open, setOpen] = useState(false);
  return (
    <Fragment>
      <Card className="w-full gap-2">
        <CardHeader className="flex items-center justify-between border-b">
          <div>
            <div className="flex gap-2">
              <CardTitle>Data Faktur</CardTitle>
              {invoices && invoices?.length > 0 ? (
                <Badge className="bg-green-100 text-primary px-1 hover:bg-green-100">
                  Total {invoices?.length}
                </Badge>
              ) : null}
            </div>
            <CardDescription>
              Berikut semua data faktur yang ada
            </CardDescription>
          </div>
          <div className="space-x-2">
            <Button variant={"outline"}>
              <FilterIcon /> Filter
            </Button>
            <Button className="cursor-pointer" onClick={() => setOpen(true)}>
              <PlusIcon /> Tambah
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <InvoiceTable
            isPending={isPending}
            invoices={invoices as IInvoiceExtended[]}
            refetch={refetch}
          />
        </CardContent>
      </Card>
      <CreateInvoiceDialog open={open} setOpen={setOpen} refetch={refetch} />
    </Fragment>
  );
}
