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
  transactionId: string;
  mutateDeleteTransaction: (id: string) => void;
  open: boolean;
  setOpen: Dispatch<SetStateAction<boolean>>;
  isPending: boolean;
}
export default function DeleteTransactionDialog(props: PropTypes) {
  const { mutateDeleteTransaction, open, setOpen, isPending, transactionId } =
    props;
  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Hapus transaksi?</AlertDialogTitle>
          <AlertDialogDescription className="w-full">
            Transaksi yang dihapus tidak dapat dikembalikan lagi
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Batal</AlertDialogCancel>
          <AlertDialogAction
            variant={"destructive"}
            onClick={() => mutateDeleteTransaction(transactionId)}
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
