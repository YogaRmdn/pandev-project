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
import { Field, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Spinner } from "@/components/ui/spinner";
import { Dispatch, SetStateAction, useState } from "react";

interface PropTypes {
  userId: string;
  userFullName: string;
  mutateDeleteUser: (id: string) => void;
  open: boolean;
  setOpen: Dispatch<SetStateAction<boolean>>;
  isPending: boolean;
}
export default function DeleteUserDialog(props: PropTypes) {
  const { mutateDeleteUser, open, setOpen, isPending, userId, userFullName } =
    props;

  const [confirmation, setConfirmation] = useState("");

  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Hapus akun?</AlertDialogTitle>
          <AlertDialogDescription className="w-full">
            Akun yang dihapus akan kehilangan akses terhadap aplikasi.
            Portfolio-portfolio yang dibuat oleh user juga akan terhapus. Apakah Anda
            yakin?
          </AlertDialogDescription>
        </AlertDialogHeader>
        <div>
          <Field>
            <FieldLabel>Ketik "{userFullName}" untuk konfirmasi</FieldLabel>
            <Input
              onChange={(e) => setConfirmation(e.target.value)}
              className="border-destructive"
            />
          </Field>
        </div>
        <AlertDialogFooter>
          <AlertDialogCancel>Batal</AlertDialogCancel>
          <AlertDialogAction
            variant={"destructive"}
            onClick={() => mutateDeleteUser(userId)}
            disabled={isPending || confirmation !== userFullName}
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
