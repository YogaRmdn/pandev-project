"use client";

import { createUser } from "@/app/actions/user-management";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
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
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { EyeIcon, EyeOffIcon } from "lucide-react";
import { Dispatch, SetStateAction, useEffect, useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { toast } from "sonner";
import z from "zod";
import { IUser } from "../_types/User";

interface PropTypes {
  selectedUser: IUser | null;
  open: boolean;
  setOpen: Dispatch<SetStateAction<boolean>>;
  refetch: () => void;
}

const formSchema = z.object({
  fullname: z.string().min(1, "Nama wajib diisi"),
  email: z.email("Email tidak valid").min(1, "Email wajib diisi"),
  password: z.string().min(8, "Password minimal 8 karakter"),
  role: z.enum(["ADMIN", "USER"], { error: "Role wajib dipilih" }),
});

export default function EditUserDialog(props: PropTypes) {
  const { open, setOpen, refetch, selectedUser } = props;
  const [showPassword, setShowPassword] = useState(false);

  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      fullname: "",
      email: "",
      password: "",
      role: "USER" as const,
    },
  });

  useEffect(() => {
    if (selectedUser) {
      form.setValue("fullname", selectedUser.fullname);
      form.setValue("role", selectedUser.role);
      form.setValue("email", selectedUser.email);
    }
  }, [selectedUser]);

  const { mutate, isPending } = useMutation({
    mutationFn: async (data: z.infer<typeof formSchema>) => createUser(data),
    onSuccess: () => {
      toast.success("User berhasil dibuat");
      form.reset();
      refetch();
      setOpen(false);
    },
    onError: (error) => {
      toast.error(error.message || "Gagal membuat user");
    },
  });
  const onSubmit = (data: z.infer<typeof formSchema>) => {
    mutate(data);
  };

  if (selectedUser) {
    return (
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Edit User</DialogTitle>
            <DialogDescription>
              Ubah informasi akun yang terdaftar di aplikasi
            </DialogDescription>
          </DialogHeader>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <FieldGroup>
              <Controller
                control={form.control}
                name="fullname"
                render={({ field, fieldState }) => (
                  <Field>
                    <FieldLabel htmlFor="fullname">Nama Lengkap</FieldLabel>
                    <Input
                      {...field}
                      id="fullname"
                      placeholder="Nama lengkap..."
                      value={field.value}
                      aria-invalid={fieldState.invalid}
                    />
                    <FieldError>{fieldState.error?.message}</FieldError>
                  </Field>
                )}
              />
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
                      value={field.value}
                      placeholder="email@example.com"
                      aria-invalid={fieldState.invalid}
                    />
                    <FieldError>{fieldState.error?.message}</FieldError>
                  </Field>
                )}
              />
              <Controller
                control={form.control}
                name="password"
                render={({ field, fieldState }) => (
                  <Field>
                    <FieldLabel htmlFor="password">Password</FieldLabel>
                    <div className="relative">
                      <Input
                        {...field}
                        id="password"
                        type={showPassword ? "text" : "password"}
                        placeholder="Minimal 8 karakter"
                        value={field.value}
                        aria-invalid={fieldState.invalid}
                        className="pr-10"
                      />
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                        onClick={() => setShowPassword(!showPassword)}
                      >
                        {showPassword ? (
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
                name="role"
                render={({ field, fieldState }) => (
                  <Field>
                    <FieldLabel htmlFor="role">Role</FieldLabel>
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger
                        id="role"
                        aria-invalid={fieldState.invalid}
                      >
                        <SelectValue placeholder="Pilih role" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="USER">USER</SelectItem>
                        <SelectItem value="ADMIN">ADMIN</SelectItem>
                      </SelectContent>
                    </Select>
                    <FieldError>{fieldState.error?.message}</FieldError>
                  </Field>
                )}
              />
            </FieldGroup>
            <DialogFooter>
              <Button
                type="button"
                variant="outline"
                onClick={() => setOpen(false)}
                disabled={isPending}
              >
                Batal
              </Button>
              <Button type="submit" disabled={isPending}>
                {isPending ? "Menyimpan..." : "Simpan"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    );
  }
}
