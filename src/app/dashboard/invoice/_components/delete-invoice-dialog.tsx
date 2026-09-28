import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { Spinner } from "@/components/ui/spinner";
import { Dispatch, SetStateAction } from "react";

interface PropTypes {
  invoiceId: string;
  mutateDeleteInvoice: (id: string) => void;
  open: boolean;
  setOpen: Dispatch<SetStateAction<boolean>>;
  isPending: boolean;
}
export default function DeleteInvoiceDialog(props: PropTypes) {
  const { mutateDeleteInvoice, open, setOpen, isPending, invoiceId } = props;
  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Hapus Tagihan?</AlertDialogTitle>
          <AlertDialogDescription className="w-full">
            Tagihan yang dihapus tidak dapat dikembalikan lagi.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Batal</AlertDialogCancel>
          <AlertDialogAction
            variant={"destructive"}
            onClick={() => mutateDeleteInvoice(invoiceId)}
            disabled={isPending}
          >
            {isPending ? (
              <>
                <Spinner variant="circle" />
                Menghapus...
              </>
            ) : (
              "Hapus"
            )}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
