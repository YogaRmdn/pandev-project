"use client";

import { Button } from "@/components/ui/button"
import { Field, FieldError, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { zodResolver } from "@hookform/resolvers/zod"
import { useMutation } from "@tanstack/react-query"
import { EyeIcon, EyeOffIcon } from "lucide-react"
import { useState } from "react"
import { Controller, useForm } from "react-hook-form"
import { toast } from "sonner"
import z from "zod"
import { updatePassword } from "@/app/actions/user"

const formSchema = z.object({
  currentPassword: z.string().min(1, "Password wajib diisi"),
  newPassword: z.string().min(8, "Password minimal 8 karakter"),
  confirmPassword: z.string().min(1, "Konfirmasi Password wajib diisi"),
}).refine((data) => data.newPassword === data.confirmPassword, {
  error: "Password tidak sama",
  path: ["confirmPassword"],
});

export default function SecurityForm() {
  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      currentPassword: "",
      newPassword: "",
      confirmPassword: ""
    }
  })

  const [visibility, setVisibility] = useState({
    currentPassword: false,
    newPassword: false,
    confirmPassword: false
  });

  const { mutate, isPending } = useMutation({
    mutationFn: async (data: { currentPassword: string; newPassword: string }) => {
      const result = await updatePassword(data);
      if (!result.success) {
        throw new Error(result.error);
      }
      return result;
    },
    onSuccess: () => {
      toast.success("Password berhasil diperbarui");
      form.reset();
    },
    onError: (error) => {
      toast.error(error.message || "Gagal memperbarui password");
    }
  });

  const handleVisibility = (target: "currentPassword" | "newPassword" | "confirmPassword") => {
    setVisibility((prev) => ({ ...prev, [target]: !prev[target] }))
  }

  const onSubmit = (data: z.infer<typeof formSchema>) => {
    mutate({ currentPassword: data.currentPassword, newPassword: data.newPassword });
  };

  return (
    <div className="py-4 px-8 mx-4 border-b flex items-center">
      <div className="w-1/2">
        <div className="font-bold">Keamanan</div>
        <div className="text-sm">Ubah password Anda</div>
        <Button type="submit" form="security-form" disabled={isPending || !form.formState.isValid} className="mt-4">{isPending ? "Menyimpan..." : "Simpan"}</Button>
      </div>
      <div className="w-full">
        <form id="security-form" className="space-y-4" onSubmit={form.handleSubmit(onSubmit)}>
          <Controller
            control={form.control}
            name="currentPassword"
            render={({ field, fieldState }) => (
              <Field>
                <FieldLabel htmlFor="currentPassword">Password Saat Ini</FieldLabel>
                <div className="relative">
                  <Input
                    {...field}
                    id="currentPassword"
                    type={visibility.currentPassword ? "text" : "password"}
                    aria-invalid={fieldState.invalid}
                    className="bg-background pr-10"
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                    onClick={() => handleVisibility("currentPassword")}
                  >
                    {visibility.currentPassword ? (
                      <EyeOffIcon className="size-4 text-muted-foreground" />
                    ) : (
                      <EyeIcon className="size-4 text-muted-foreground" />
                    )}
                  </Button>
                </div>
                <FieldError>{fieldState.error?.message}</FieldError>
              </Field>
            )}
          />
          <Controller
            control={form.control}
            name="newPassword"
            render={({ field, fieldState }) => (
              <Field>
                <FieldLabel htmlFor="newPassword">Password Baru</FieldLabel>
                <div className="relative">
                  <Input
                    {...field}
                    id="newPassword"
                    type={visibility.newPassword ? "text" : "password"}
                    aria-invalid={fieldState.invalid}
                    className="bg-background pr-10"
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                    onClick={() => handleVisibility("newPassword")}
                  >
                    {visibility.newPassword ? (
                      <EyeOffIcon className="size-4 text-muted-foreground" />
                    ) : (
                      <EyeIcon className="size-4 text-muted-foreground" />
                    )}
                  </Button>
                </div>
                <FieldError>{fieldState.error?.message}</FieldError>
              </Field>
            )}
          />
          <Controller
            control={form.control}
            name="confirmPassword"
            render={({ field, fieldState }) => (
              <Field>
                <FieldLabel htmlFor="confirmPassword">Konfirmasi Password</FieldLabel>
                <div className="relative">
                  <Input
                    {...field}
                    id="confirmPassword"
                    type={visibility.confirmPassword ? "text" : "password"}
                    aria-invalid={fieldState.invalid}
                    className="bg-background pr-10"
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                    onClick={() => handleVisibility("confirmPassword")}
                  >
                    {visibility.confirmPassword ? (
                      <EyeOffIcon className="size-4 text-muted-foreground" />
                    ) : (
                      <EyeIcon className="size-4 text-muted-foreground" />
                    )}
                  </Button>
                </div>
                <FieldError>{fieldState.error?.message}</FieldError>
              </Field>
            )}
          />
        </form>
      </div>
    </div>
  )
}
