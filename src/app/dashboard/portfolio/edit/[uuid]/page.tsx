"use client";

import { Suspense } from "react";
import { useParams } from "next/navigation";
import EditPortfolioForm from "./_components/edit-portfolio-form";

function EditPageContent() {
  const params = useParams();
  const uuid = params.uuid as string;
  return <EditPortfolioForm uuid={uuid} />;
}

function EditPageSkeleton() {
  return (
    <div className="space-y-4">
      <div className="pb-2 border-b">
        <div className="text-xl font-bold">Edit Portfolio</div>
        <div>Memuat data portfolio...</div>
      </div>
      <div className="h-96 bg-muted animate-pulse rounded-lg" />
    </div>
  );
}

export default function EditPortfolioPage() {
  return (
    <Suspense fallback={<EditPageSkeleton />}>
      <EditPageContent />
    </Suspense>
  );
}
