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
import { Input } from "@/components/ui/input";
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Spinner } from "@/components/ui/spinner";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { useMutation } from "@tanstack/react-query";
import { ExternalLinkIcon, PencilIcon, Trash2Icon } from "lucide-react";
import { Fragment } from "react/jsx-runtime";
import { toast } from "sonner";
import { useState } from "react";
import { IInvoiceExtended } from "../_types/Invoice";
import { deleteInvoice } from "@/app/actions/invoice";
import DeleteInvoiceDialog from "./delete-invoice-dialog";

import InvoiceTableAction from "./invoice-table-action";
import DetailInvoiceDialog from "./detail-invoice-dialog";
import { useDisclosure } from "@/hooks/use-disclosure";
import { cn, getInvoiceStatus } from "@/lib/utils";
import EditInvoiceDialog from "./edit-invoice-dialog";

const TABLE_HEADER = ["#", "Date", "Description", "Status", "Action"];

interface PropTypes {
  isPending: boolean;
  invoices: IInvoiceExtended[] | undefined;
  refetch: () => void;
}

export default function InvoiceTable(props: PropTypes) {
  const { invoices, isPending, refetch } = props;

  const [selectedInvoice, setSelectedInvoice] =
    useState<IInvoiceExtended | null>(null);

  const { open: openDeleteDialog, setOpen: setOpenDeleteDialog } =
    useDisclosure();
  const { open: openDetailDialog, setOpen: setOpenDetailDialog } =
    useDisclosure();
  const { open: openEditDialog, setOpen: setOpenEditDialog } = useDisclosure();

  const getTableActions = (invoice: IInvoiceExtended) => [
    {
      label: "Detail",
      icon: <ExternalLinkIcon />,
      handleClick: () => {
        setSelectedInvoice(invoice);
        setOpenDetailDialog(true);
      },
    },
    {
      label: "Edit",
      icon: <PencilIcon />,
      handleClick: () => {
        setSelectedInvoice(invoice);
        setOpenEditDialog(true);
      },
    },
    {
      label: "Hapus",
      icon: <Trash2Icon />,
      handleClick: () => {
        setOpenDeleteDialog(true);
      },
      isDestructive: true,
    },
  ];

  const { mutate: mutateDeleteInvoice, isPending: isPendingDeleteInvoice } =
    useMutation({
      mutationFn: (id: string) => deleteInvoice(id),
      onSuccess: () => {
        refetch();
        toast.success("Tagihan berhasil dihapus");
        setSelectedInvoice(null);
      },
      onError: () => {
        toast.error("Gagal menghapus tagihan");
      },
    });

  return (
    <Fragment>
      <Table>
        <TableHeader>
          <TableRow>
            {TABLE_HEADER.map((header) => (
              <TableHead key={`th-${header}`}>{header}</TableHead>
            ))}
          </TableRow>
        </TableHeader>
        <TableBody>
          {invoices?.map((invoice, index) => {
            return (
              <TableRow key={invoice.id}>
                <TableCell>{index + 1}</TableCell>
                <TableCell>{invoice.date.toLocaleDateString()}</TableCell>
                <TableCell>{invoice.description}</TableCell>
                <TableCell>
                  <Badge
                    className={cn(
                      "text-black",
                      invoice.status === "PAID" && "bg-green-100 hover:bg-green-100",
                      invoice.status === "PARTIALLY_PAID" && "bg-yellow-100 hover:bg-yellow-100",
                      invoice.status === "UNPAID" && "bg-red-100 hover:bg-red-100",
                    )}
                  >
                    {getInvoiceStatus(invoice.status)}
                  </Badge>
                </TableCell>
                <TableCell className="flex gap-2">
                  {getTableActions(invoice).map((action, index) => (
                    <InvoiceTableAction
                      key={`table-action-${index}`}
                      handleClick={action.handleClick}
                      icon={action.icon}
                      label={action.label}
                      isDestructive={action.isDestructive}
                    />
                  ))}
                </TableCell>
              </TableRow>
            );
          })}
        </TableBody>
        {isPending && (
          <TableCaption className="mb-4">
            <Spinner variant="circle" />
          </TableCaption>
        )}
        {!isPending && invoices?.length === 0 && (
          <TableCaption className="mb-4">Belum ada data transaksi</TableCaption>
        )}
      </Table>
      {/* <div className="flex items-center justify-between mt-4">
            <div className="flex items-center gap-2">
              <div className="text-sm text-muted-foreground">Rows per page</div>
              <Select
                value={limit.toString()}
                onValueChange={(value) => {
                  setLimit(Number(value));
                  setPage(1);
                }}
              >
                <SelectTrigger className="w-20">
                  <SelectValue placeholder={limit.toString()} />
                </SelectTrigger>
                <SelectContent>
                  {[1, 10, 20, 50, 100].map((size) => (
                    <SelectItem key={`limit-${size}`} value={size.toString()}>
                      {size}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            {transactions?.totalPages && transactions?.totalPages > 1 ? (
              <Pagination className="w-auto mx-0">
                <PaginationContent>
                  <PaginationItem>
                    <PaginationPrevious
                      onClick={() =>
                        page === 1
                          ? setPage(Number(transactions?.totalPages))
                          : setPage(page - 1)
                      }
                    />
                  </PaginationItem>
                  <PaginationItem>
                    <PaginationNext
                      onClick={() =>
                        page === Number(transactions?.totalPages)
                          ? setPage(1)
                          : setPage(page + 1)
                      }
                    />
                  </PaginationItem>
                </PaginationContent>
              </Pagination>
            ) : (
              ''
            )}
          </div> */}
      <DeleteInvoiceDialog
        invoiceId={selectedInvoice?.id ?? ""}
        open={openDeleteDialog}
        setOpen={setOpenDeleteDialog}
        mutateDeleteInvoice={mutateDeleteInvoice}
        isPending={isPendingDeleteInvoice}
      />
      <EditInvoiceDialog
        open={openEditDialog}
        setOpen={setOpenEditDialog}
        invoice={selectedInvoice}
        setSelectedInvoice={setSelectedInvoice}
        refetch={refetch}
      />
      <DetailInvoiceDialog
        invoice={selectedInvoice}
        open={openDetailDialog}
        setOpen={setOpenDetailDialog}
        refetch={refetch}
        setSelectedInvoice={setSelectedInvoice}
      />
    </Fragment>
  );
}
