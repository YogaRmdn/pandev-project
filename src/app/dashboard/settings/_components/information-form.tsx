"use client";

import { Button } from "@/components/ui/button";
import { Field, FieldError, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Controller, useForm } from "react-hook-form";
import { toast } from "sonner";
import z from "zod";
import { updateUserProfile } from "@/app/actions/user";

interface PropTypes {
  userEmail?: string;
  userFullname?: string;
  refetchSession: () => void;
}

const formSchema = z.object({
  fullname: z.string().min(1, "Nama wajib diisi"),
  email: z.email("Email invalid").min(1, "Email wajib diisi"),
});

export default function InformationForm(props: PropTypes) {
  const { userEmail, userFullname, refetchSession } = props;
  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      fullname: userFullname ?? "",
      email: userEmail ?? "",
    },
  });

  const { mutate, isPending } = useMutation({
    mutationFn: (data: z.infer<typeof formSchema>) =>
      updateUserProfile(data),
    onSuccess: () => {
      toast.success("Profil berhasil diperbarui");
      refetchSession();
    },
    onError: (error) => {
      toast.error("Gagal memperbarui profil");
    },
  });

  const onSubmit = (data: z.infer<typeof formSchema>) => {
    mutate(data);
  };

  return (
    <div className="py-4 px-8 mx-4 border-b flex items-center">
      <div className="w-1/2">
        <div className="font-bold">Data Diri</div>
        <div className="text-sm">Ubah informasi data diri Anda</div>
        <Button
          className="mt-4"
          type="submit"
          form="information-form"
          disabled={isPending}
        >
          {isPending ? "Menyimpan..." : "Simpan"}
        </Button>
      </div>
      <div className="w-full">
        <form
          id="information-form"
          className="space-y-4"
          onSubmit={form.handleSubmit(onSubmit)}
        >
          <Controller
            control={form.control}
            name="email"
            render={({ field, fieldState }) => (
              <Field>
                <FieldLabel htmlFor="email">Email</FieldLabel>
                <Input
                  {...field}
                  id="email"
                  type="email"
                  aria-invalid={fieldState.invalid}
                  placeholder="Email..."
                  className="bg-background"
                  autoComplete="off"
                />
                <FieldError>{fieldState.error?.message}</FieldError>
              </Field>
            )}
          />
          <Controller
            control={form.control}
            name="fullname"
            render={({ field, fieldState }) => (
              <Field>
                <FieldLabel htmlFor="fullname">Nama Lengkap</FieldLabel>
                <Input
                  {...field}
                  id="fullname"
                  type="string"
                  aria-invalid={fieldState.invalid}
                  placeholder="Nama Lengkap..."
                  className="bg-background"
                  autoComplete="off"
                />
                <FieldError>{fieldState.error?.message}</FieldError>
              </Field>
            )}
          />
        </form>
      </div>
    </div>
  );
}
