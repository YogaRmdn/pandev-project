"use client";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import {
  Field,
  FieldError,
  FieldGroup,
  FieldLabel,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import z from "zod";
import { useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { signIn } from "@/lib/auth-client";
import { EyeIcon, EyeOffIcon } from "lucide-react";

const formSchema = z.object({
  email: z.email("Email tidak valid").min(1, "Email harus diisi"),
  password: z.string().min(1, "Password harus diisi"),
});

export function LoginForm({
  className,
  ...props
}: React.ComponentProps<"form">) {
  const router = useRouter();
  const [showPassword, setShowPassword] = useState(false);
  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: "",
      password: ""
    }
  });

  const { isPending, mutate } = useMutation({
    mutationFn: async (data: z.infer<typeof formSchema>) => {
      const { error } = await signIn.email({
        email: data.email,
        password: data.password,
      });

      if (error) {
        throw new Error("Email atau password salah");
      }
    },
    onSuccess: () => {
      router.push("/dashboard");
      toast.success("Login berhasil")
      router.refresh();
    },
    onError: (error) => {
      toast.error(error.message || "Login gagal. Coba lagi");
    },
  });

  const onSubmit = (data: z.infer<typeof formSchema>) => {
    // console.log(data)
    mutate(data);
  };

  return (
    <form
      className={cn("flex flex-col gap-6", className)}
      onSubmit={form.handleSubmit(onSubmit)}
      {...props}
    >
      <FieldGroup>
        <div className="flex flex-col items-center gap-1 text-center">
          <h1 className="text-2xl font-bold">Login</h1>
          <p className="text-sm text-balance text-muted-foreground">
            Masukkan email Anda untuk login ke akun Anda
          </p>
        </div>
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
                placeholder="m@example.com"
                className="bg-background"
                autoComplete="off"
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
              <div className="flex items-center">
                <FieldLabel htmlFor="password">Password</FieldLabel>
              </div>
              <div className="relative">
                <Input
                  {...field}
                  id="password"
                  type={showPassword ? "text" : "password"}
                  aria-invalid={fieldState.invalid}
                  className="bg-background pr-10"
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
        <Field>
          <Button type="submit" disabled={isPending}>
            {isPending ? "Memuat..." : "Login"}
          </Button>
        </Field>
      </FieldGroup>
    </form>
  );
}
