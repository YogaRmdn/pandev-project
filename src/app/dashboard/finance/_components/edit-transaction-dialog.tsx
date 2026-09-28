"use client";

import { createTransaction } from "@/app/actions/transaction";
import { Spinner } from "@/components/ui/spinner";
import { Button } from "@/components/ui/button";

import {
  Field,
  FieldError,
  FieldGroup,
  FieldLabel,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Controller, useForm } from "react-hook-form";
import { toast } from "sonner";
import z from "zod";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Dispatch, SetStateAction, useEffect, useRef } from "react";
import { Transaction } from "@prisma/client";
import { Textarea } from "@/components/ui/textarea";

const formSchema = z.object({
  type: z.string().min(1, "Tipe wajib diisi"),
  description: z.string().min(1, "Deskripsi wajib diisi"),
  date: z.string().min(1, "Tanggal wajib diisi"),
  amount: z.coerce.number().min(1, "Total wajib diisi"),
});

interface PropTypes {
  open: boolean;
  setOpen: Dispatch<SetStateAction<boolean>>;
  transaction: Transaction | null;
  setSelectedTransaction: Dispatch<SetStateAction<Transaction | null>>;
  refetch: () => void;
}

export default function EditTransactionDialog(props: PropTypes) {
  const { open, setOpen, refetch, transaction, setSelectedTransaction } = props;

  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      type: "",
      description: "",
      date: new Date().toISOString().split("T")[0],
      amount: 0,
    },
  });

  const { mutate, isPending } = useMutation({
    mutationFn: createTransaction,
    onSuccess: () => {
      toast.success("Berhasil membuat transaksi");
      form.reset();
      setOpen(false);
      setSelectedTransaction(null);
      refetch();
    },
    onError: (error) => {
      toast.error(`Gagal membuat data keuangan: ${error.message}`);
    },
  });

  const onSubmit = (data: z.infer<typeof formSchema>) => {
    mutate(data);
  };

  const formRef = useRef<HTMLFormElement>(null);

  useEffect(() => {
    if (transaction) {
      form.setValue("type", transaction.type);
      form.setValue("description", transaction.description);
      form.setValue("amount", transaction.amount);
      form.setValue("date", transaction.date.toISOString().split("T")[0]);
    }
  }, [transaction]);

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogContent>
        <DialogHeader className="border-b pb-2">
          <DialogTitle>Edit Transaksi</DialogTitle>
          <DialogDescription>
            Edit transaksi yang sudah dibuat
          </DialogDescription>
        </DialogHeader>
        <form
          ref={formRef}
          className="max-h-100 overflow-y-auto"
          onSubmit={(e) => form.handleSubmit(onSubmit)(e)}
          onKeyDown={(e) => {
            if (e.key === "Enter") e.preventDefault();
          }}
        >
          <FieldGroup className="gap-3">
            <Controller
              control={form.control}
              name="type"
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel>Tipe Transaksi</FieldLabel>
                  <Select value={field.value} onValueChange={field.onChange}>
                    <SelectTrigger aria-invalid={fieldState.invalid}>
                      <SelectValue placeholder="Pilih tipe transaksi" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem value="INCOME">Pemasukan</SelectItem>
                        <SelectItem value="EXPENSE">Pengeluaran</SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <FieldError>{fieldState.error?.message}</FieldError>
                </Field>
              )}
            />

            <Controller
              control={form.control}
              name={"amount"}
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel htmlFor={"input-amount"}>Total</FieldLabel>
                  <Input
                    id={"input-amount"}
                    type="text"
                    inputMode="numeric"
                    aria-invalid={fieldState.invalid}
                    placeholder="Rp 0"
                    value={
                      field.value
                        ? new Intl.NumberFormat("id-ID").format(
                            field.value as number,
                          )
                        : ""
                    }
                    onChange={(e) => {
                      const raw = e.target.value.replace(/[^0-9]/g, "");
                      field.onChange(raw ? Number(raw) : 0);
                    }}
                  />
                  <FieldError>{fieldState.error?.message}</FieldError>
                </Field>
              )}
            />

            <Controller
              control={form.control}
              name="description"
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel htmlFor="description-form">Deskripsi</FieldLabel>
                  <Textarea
                    {...field}
                    id="description-form"
                    autoComplete="off"
                    aria-invalid={fieldState.invalid}
                    placeholder="Deskripsi transaksi..."
                  />
                  <FieldError>{fieldState.error?.message}</FieldError>
                </Field>
              )}
            />
            <Controller
              control={form.control}
              name="date"
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel htmlFor="date-form">Tanggal</FieldLabel>
                  <Input
                    {...field}
                    id="date-form"
                    type="date"
                    aria-invalid={fieldState.invalid}
                  />
                  <FieldError>{fieldState.error?.message}</FieldError>
                </Field>
              )}
            />
          </FieldGroup>
          <Button
            type="submit"
            className="w-full h-10 mt-4"
            disabled={isPending}
          >
            {isPending ? (
              <div className="flex items-center gap-2">
                <Spinner size="sm" />
                Menyimpan...
              </div>
            ) : (
              "Submit"
            )}
          </Button>
        </form>
      </DialogContent>
    </Dialog>
  );
}
