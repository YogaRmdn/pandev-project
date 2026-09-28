"use client";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Spinner } from "@/components/ui/spinner";
import { Textarea } from "@/components/ui/textarea";
import { MailIcon } from "lucide-react";
import { useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { toast } from "sonner";
import z from "zod";
import { FieldError } from "@/components/ui/field";

const formSchema = z.object({
  email: z.email("Email invalid").min(1, "Email wajib diisi"),
  name: z.string().min(1, "Nama wajib diisi"),
  message: z.string().min(1, "Pesan wajib diisi"),
});

export default function ContactForm() {
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: "",
      email: "",
      message: "",
    },
  });

  const [isPending, setIsPending] = useState(false);

  const onSubmit = async (data: z.infer<typeof formSchema>) => {
    setIsPending(true);
    const formData = new FormData();
    formData.append("access_key", "3684fb31-4fd7-40f7-9afa-53ef3da6e9a6");
    formData.append("subject", "Email baru dari PanDev");
    formData.append("email", data.email);
    formData.append("name", data.name);
    formData.append("message", data.message);
    const res = await fetch("https://api.web3forms.com/submit", {
      method: "POST",
      body: formData,
    });
    if (res.ok) {
      toast.success("Email berhasil dikirim");
      setIsPending(false);
      return;
    }
    toast.error("Email gagal dikirim");
    setIsPending(false);
  };
  return (
    <section className="relative min-h-screen flex items-center justify-center py-16">
      <div
        className="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style={{ backgroundImage: "url('/assets/common/contact-bg.png')" }}
      />
      <div className="absolute inset-0 bg-black/80" />
      <div className="relative z-10 w-full max-w-lg mx-4 p-8 rounded-2xl bg-[#1b1b1b]">
        <div className="text-center mb-8">
          <h2 className="text-3xl font-bold mt-1 text-white">Hubungi Kami</h2>
        </div>
        <form
          onSubmit={form.handleSubmit(onSubmit)}
          className="space-y-6 text-white"
        >
          <Controller
            control={form.control}
            name="name"
            render={({ field, fieldState }) => (
              <div className="space-y-2">
                <Label htmlFor="name">Nama</Label>
                <Input
                  {...field}
                  id="name"
                  placeholder="Masukkan nama Anda..."
                  aria-invalid={fieldState.invalid}
                  className="bg-white/5 border-white/20 placeholder:text-white/70"
                />
                <FieldError>{fieldState.error?.message}</FieldError>
              </div>
            )}
          />
          <Controller
            control={form.control}
            name="email"
            render={({ field, fieldState }) => (
              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  {...field}
                  id="email"
                  type="email"
                  aria-invalid={fieldState.invalid}
                  placeholder="Masukkan email Anda..."
                  className="bg-white/5 border-white/20 placeholder:text-white/70"
                />
                <FieldError>{fieldState.error?.message}</FieldError>
              </div>
            )}
          />
          <Controller
            control={form.control}
            name="message"
            render={({ field, fieldState }) => (
              <div className="space-y-2">
                <Label htmlFor="message">Pesan</Label>
                <Textarea
                  {...field}
                  id="message"
                  rows={5}
                  aria-invalid={fieldState.invalid}
                  placeholder="Tulis pesan disini..."
                  className="w-full min-h-30 rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-base focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 outline-none resize-none placeholder:text-white/70"
                />
                <FieldError>{fieldState.error?.message}</FieldError>
              </div>
            )}
          />
          <div className="flex justify-end">
            <Button
              className="px-4"
              disabled={isPending}
              type="submit"
              size="lg"
            >
              {isPending ? <Spinner variant="circle" /> : <MailIcon />}
              {isPending ? "Mengirim..." : "Kirim"}
            </Button>
          </div>
        </form>
      </div>
    </section>
  );
}
