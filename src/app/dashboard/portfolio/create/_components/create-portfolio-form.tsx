"use client";

import { createPortfolio } from "@/app/actions/portfolio";
import { deleteMedia } from "@/app/actions/media";
import { Spinner } from "@/components/ui/spinner";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Field, FieldError, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Controller, useForm } from "react-hook-form";
import { toast } from "sonner";
import z from "zod";
import ThumbnailDropzone from "../../_components/thumbnail-dropzone";
import GaleryDropzone from "../../_components/galery-dropzone";
import TechStackSelect from "../../_components/tech-stack-select";
import { useEffect, useRef, useState } from "react";
import { PORTFOLIO_CATEGORIES } from "../../_constants/categories";
import { useRouter } from "next/navigation";

const formSchema = z.object({
  thumbnail: z.string().optional(),
  name: z.string().min(1, "Nama wajib diisi"),
  status: z.string().min(1, "Status wajib diisi"),
  category: z.string().min(1, "Kategori wajib diisi"),
  description: z.string().min(1, "Deskripsi wajib diisi"),
  demo_link: z.string().optional(),
  repository_link: z.string().optional(),
  galery: z.array(z.string()).optional(),
});

export default function CreatePortfolioForm() {
  const router = useRouter();
  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      thumbnail: "",
      name: "",
      category: "",
      description: "",
      demo_link: "",
      repository_link: "",
      status: "",
      galery: [] as string[],
    },
  });

  const [techStacks, setTechStacks] = useState<string[]>([]);
  const uploadedFilesRef = useRef<string[]>([]);

  const thumbnailValue = form.watch("thumbnail");
  const galleryValue = form.watch("galery");

  useEffect(() => {
    if (thumbnailValue && !uploadedFilesRef.current.includes(thumbnailValue)) {
      uploadedFilesRef.current.push(thumbnailValue);
    }
  }, [thumbnailValue]);

  useEffect(() => {
    if (galleryValue) {
      for (const url of galleryValue) {
        if (!uploadedFilesRef.current.includes(url)) {
          uploadedFilesRef.current.push(url);
        }
      }
    }
  }, [galleryValue]);

  useEffect(() => {
    const handleBeforeUnload = (e: BeforeUnloadEvent) => {
      if (uploadedFilesRef.current.length > 0) {
        e.preventDefault();
      }
    };
    const cleanup = () => {
      for (const url of uploadedFilesRef.current) {
        deleteMedia(url);
      }
    };
    window.addEventListener("beforeunload", handleBeforeUnload);
    return () => {
      window.removeEventListener("beforeunload", handleBeforeUnload);
      cleanup();
    };
  }, []);

  const { mutate, isPending } = useMutation({
    mutationFn: createPortfolio,
    onSuccess: () => {
      toast.success("Portfolio berhasi dibuat");
      router.push("/dashboard/portfolio")
    },
    onError: (error) => {
      toast.error(`Gagal membuat portofolio`);
    },
  });

  const onSubmit = (data: z.infer<typeof formSchema>) => {
    mutate({ ...data, tech_stacks: techStacks });
  };

  return (
    <>
      <Card>
        <CardContent>
          <form
            onSubmit={(e) => form.handleSubmit(onSubmit)(e)}
            onKeyDown={(e) => {
              if (e.key === "Enter") e.preventDefault();
            }}
          >
            <Tabs defaultValue="informasi">
              <TabsList className="h-9! px-1 mb-4" variant={"default"}>
                <TabsTrigger className="p-4" value="informasi">
                  Informasi
                </TabsTrigger>
                <TabsTrigger className="p-4" value="media">
                  Media
                </TabsTrigger>
              </TabsList>
              <TabsContent value="informasi">
                <div className="flex flex-col gap-4">
                  <Controller
                    control={form.control}
                    name="thumbnail"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel>Thumbnail</FieldLabel>
                        <ThumbnailDropzone
                          isinvalid={`${fieldState.invalid}`}
                          value={field.value}
                          onChange={field.onChange}
                        />
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                  <Controller
                    control={form.control}
                    name="name"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel htmlFor="name-form">Nama</FieldLabel>
                        <Input
                          {...field}
                          id="name"
                          aria-invalid={fieldState.invalid}
                          placeholder="Nama projek..."
                          autoComplete="off"
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
                        <FieldLabel htmlFor="description-form">
                          Deskripsi
                        </FieldLabel>
                        <Textarea
                          {...field}
                          id="description-form"
                          aria-invalid={fieldState.invalid}
                          placeholder="Deskripsi..."
                        />
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                  <Controller
                    control={form.control}
                    name="status"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel htmlFor="status-form">Status</FieldLabel>
                        <Select
                          value={field.value}
                          onValueChange={field.onChange}
                        >
                          <SelectTrigger aria-invalid={fieldState.invalid}>
                            <SelectValue
                              id="status-form"
                              placeholder="Pilih status"
                            />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectGroup>
                              <SelectItem value="draft">Draft</SelectItem>
                              <SelectItem value="published">Publish</SelectItem>
                            </SelectGroup>
                          </SelectContent>
                        </Select>
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                  <Controller
                    control={form.control}
                    name="category"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel htmlFor="category-form">
                          Kategori
                        </FieldLabel>
                        <Select
                          value={field.value}
                          onValueChange={field.onChange}
                        >
                          <SelectTrigger aria-invalid={fieldState.invalid}>
                            <SelectValue
                              id="category-form"
                              placeholder="Pilih kategori"
                            />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectGroup>
                              {PORTFOLIO_CATEGORIES.map((category) => (
                                <SelectItem key={category} value={category}>
                                  {category}
                                </SelectItem>
                              ))}
                            </SelectGroup>
                          </SelectContent>
                        </Select>
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                  <Controller
                    control={form.control}
                    name="demo_link"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel htmlFor="demo-link-form">
                          Link Demo
                        </FieldLabel>
                        <Input
                          {...field}
                          id="demo-link-form"
                          aria-invalid={fieldState.invalid}
                          placeholder="Link demo..."
                          autoComplete="off"
                        />
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                  <Controller
                    control={form.control}
                    name="repository_link"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel htmlFor="repo-link-form">
                          Link Repository
                        </FieldLabel>
                        <Input
                          {...field}
                          id="repo-link-form"
                          aria-invalid={fieldState.invalid}
                          placeholder="Link repository..."
                          autoComplete="off"
                        />
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                  <Field>
                    <FieldLabel>Tech Stacks</FieldLabel>
                    <TechStackSelect
                      value={techStacks}
                      onChange={setTechStacks}
                    />
                  </Field>
                </div>
              </TabsContent>
              <TabsContent value="media">
                <div>
                  <Controller
                    control={form.control}
                    name="galery"
                    render={({ field, fieldState }) => (
                      <Field>
                        <FieldLabel>Galeri</FieldLabel>
                        <GaleryDropzone
                          onChange={field.onChange}
                          value={field.value || []}
                        />
                        <FieldError>{fieldState.error?.message}</FieldError>
                      </Field>
                    )}
                  />
                </div>
              </TabsContent>
            </Tabs>
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
        </CardContent>
      </Card>
    </>
  );
}
