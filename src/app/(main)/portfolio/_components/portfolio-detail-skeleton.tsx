import { Skeleton } from "@/components/ui/skeleton";

export default function PortfolioDetailSkeleton() {
  return (
    <div className="space-y-6 p-4">
      {/* Main Image Skeleton */}
      <Skeleton className="w-full aspect-video rounded-lg" />

      {/* Thumbnails Skeleton - Horizontal */}
      <div className="flex gap-2 overflow-x-auto pb-2">
        {[1, 2, 3].map((i) => (
          <Skeleton
            key={i}
            className="w-24 h-16 flex-shrink-0 rounded-lg"
          />
        ))}
      </div>

      {/* Info Skeleton */}
      <div className="space-y-4">
        <div>
          <Skeleton className="h-8 w-64" />
          <Skeleton className="h-5 w-24 mt-2" />
        </div>
        <div className="space-y-2">
          <Skeleton className="h-4 w-full" />
          <Skeleton className="h-4 w-full" />
          <Skeleton className="h-4 w-3/4" />
        </div>
        <div>
          <Skeleton className="h-4 w-24 mb-2" />
          <div className="flex gap-2">
            <Skeleton className="h-6 w-16" />
            <Skeleton className="h-6 w-20" />
            <Skeleton className="h-6 w-14" />
          </div>
        </div>
        <div className="flex gap-3 pt-4">
          <Skeleton className="h-10 w-32" />
          <Skeleton className="h-10 w-36" />
        </div>
      </div>
    </div>
  );
}
