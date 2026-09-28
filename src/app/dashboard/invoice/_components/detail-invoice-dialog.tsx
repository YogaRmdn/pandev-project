import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Dispatch, SetStateAction } from "react";
import Image from "next/image";
import { Button } from "@/components/ui/button";
import {
  BadgeCheckIcon,
  CircleDashedCheckIcon,
  DownloadIcon,
  PrinterIcon,
} from "lucide-react";
import { format } from "date-fns";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { convertToIDR } from "@/lib/utils";
import { pdf } from "@react-pdf/renderer";
import { saveAs } from "file-saver";
import { IInvoiceExtended } from "../_types/Invoice";
import InvoiceDocument from "./invoice-document";
import { useMutation } from "@tanstack/react-query";
import { updateInvoiceStatus } from "@/app/actions/invoice";
import { Spinner } from "@/components/ui/spinner";
import { toast } from "sonner";

interface PropTypes {
  invoice: IInvoiceExtended | null;
  open: boolean;
  setOpen: Dispatch<SetStateAction<boolean>>;
  refetch: () => void;
  setSelectedInvoice: Dispatch<SetStateAction<IInvoiceExtended | null>>;
}

export default function DetailInvoiceDialog(props: PropTypes) {
  const { invoice, open, setOpen, refetch, setSelectedInvoice } = props;

  const handleDownload = async (invoice: IInvoiceExtended | null) => {
    if (invoice) {
      const filename = `invoice-${invoice.id}`.toUpperCase();
      const blob = await pdf(<InvoiceDocument invoice={invoice} />).toBlob();
      saveAs(blob, filename);
      return;
    }
    return;
  };

  const { mutate: updateStatus, isPending } = useMutation({
    mutationFn: ({
      id,
      status,
    }: {
      id: string;
      status: "PARTIALLY_PAID" | "PAID";
    }) => updateInvoiceStatus(id, status),
    onSuccess: () => {
      toast.success("Berhasil mengubah status invoice");
      setOpen(false);
      refetch();
      setSelectedInvoice(null);
    },
    onError: () => {
      toast.success("Gagal mengubah status invoice");
    },
  });

  if (invoice) {
    let totalPrice = 0;
    invoice.invoice_items.map(
      (item) => (totalPrice += item.quantity * item.price),
    );
    return (
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogContent className="min-w-lg">
          <DialogHeader className="flex-row items-center px-4 pt-4">
            <Image
              src={"/assets/common/logo.png"}
              width={50}
              height={50}
              alt="Brand Logo"
            />
            <div>
              <DialogTitle className="text-primary font-bold!">
                Pandev
              </DialogTitle>
              <DialogDescription>Digital Agency Indonesia</DialogDescription>
            </div>
          </DialogHeader>
          <div className="uppercase font-bold text-primary flex items-center gap-4">
            <div className="h-1 w-full bg-primary"></div>
            <div>INVOICE</div>
            <div className="h-1 w-1/8 bg-primary"></div>
          </div>
          <div>
            <div className="mb-2">
              Tanggal:{" "}
              <span className="text-muted-foreground">
                {format(invoice?.date.toISOString(), "dd-MM-yyyy")}
              </span>
            </div>
            <Table className="border">
              <TableHeader>
                <TableRow>
                  <TableHead>#</TableHead>
                  <TableHead>Item Transaksi</TableHead>
                  <TableHead>Harga</TableHead>
                  <TableHead>Jumlah</TableHead>
                  <TableHead>Total</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {invoice.invoice_items.map((item, index) => (
                  <TableRow key={`invoice-item-${index}`}>
                    <TableCell>{index + 1}</TableCell>
                    <TableCell>{item.name}</TableCell>
                    <TableCell>{convertToIDR(item.price)}</TableCell>
                    <TableCell>{item.quantity}</TableCell>
                    <TableCell>
                      {convertToIDR(item.quantity * item.price)}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
          <div className="ml-auto w-fit bg-primary text-white p-3 font-semibold">
            Total: {convertToIDR(totalPrice)}
          </div>
          <DialogFooter>
            {invoice.status === "UNPAID" ? (
              <>
                <Button
                  disabled={isPending}
                  onClick={() =>
                    updateStatus({ id: invoice.id, status: "PARTIALLY_PAID" })
                  }
                  className="bg-amber-700 hover:bg-amber-700/90"
                >
                  {isPending ? (
                    <>
                      <Spinner variant="circle" /> Mengupdate status...
                    </>
                  ) : (
                    <>
                      <CircleDashedCheckIcon /> Sebagian dibayar / DP
                    </>
                  )}
                </Button>
                <Button
                  disabled={isPending}
                  onClick={() =>
                    updateStatus({ id: invoice.id, status: "PAID" })
                  }
                >
                  {isPending ? (
                    <>
                      <Spinner variant="circle" /> Mengupdate status...
                    </>
                  ) : (
                    <>
                      <BadgeCheckIcon /> Lunas
                    </>
                  )}
                </Button>
              </>
            ) : invoice.status === "PARTIALLY_PAID" ? (
              <Button disabled={isPending}>
                <BadgeCheckIcon /> Lunas
              </Button>
            ) : null}
            <Button
              disabled={isPending}
              onClick={() => handleDownload(invoice)}
              variant={"outline"}
            >
              <PrinterIcon /> Cetak Invoice
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    );
  }
}
