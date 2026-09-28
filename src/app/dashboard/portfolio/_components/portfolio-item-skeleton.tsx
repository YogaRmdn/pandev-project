import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export default function PortfolioItemSkeleton() {
  return (
    <Card>
      <CardHeader>
        <Skeleton className="w-full aspect-video rounded-tl-lg rounded-tr-lg" />
      </CardHeader>
      <CardContent>
        <div className="space-y-3">
          <div className="flex items-center justify-between pb-2 border-b">
            <Skeleton className="h-5 w-32" />
            <Skeleton className="h-8 w-8" />
          </div>
          <div className="flex items-center gap-1">
            <Skeleton className="h-4 w-4" />
            <Skeleton className="h-4 w-40" />
          </div>
          <div className="space-y-1">
            <Skeleton className="h-4 w-28" />
            <div className="flex gap-1">
              <Skeleton className="h-6 w-20" />
              <Skeleton className="h-6 w-16" />
            </div>
          </div>
          <div className="space-y-1">
            <Skeleton className="h-4 w-20" />
            <div className="flex gap-1">
              <Skeleton className="h-6 w-16" />
              <Skeleton className="h-6 w-16" />
              <Skeleton className="h-6 w-16" />
              <Skeleton className="h-6 w-8" />
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
