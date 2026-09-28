import { Card, CardContent, CardHeader } from "@/components/ui/card";

export default function PortfolioCardSkeleton() {
  return (
    <Card>
      <CardHeader>
        <div className="w-full h-48 bg-muted animate-pulse rounded-tl-lg rounded-tr-lg" />
      </CardHeader>
      <CardContent>
        <div className="space-y-3">
          <div className="pb-2 border-b">
            <div className="h-5 w-3/4 bg-muted animate-pulse rounded" />
          </div>
          <div className="h-4 w-1/3 bg-muted animate-pulse rounded" />
          <div className="space-y-1">
            <div className="h-3 w-20 bg-muted animate-pulse rounded" />
            <div className="h-6 w-24 bg-muted animate-pulse rounded" />
          </div>
          <div className="space-y-1">
            <div className="h-3 w-full bg-muted animate-pulse rounded" />
            <div className="h-3 w-2/3 bg-muted animate-pulse rounded" />
          </div>
          <div className="space-y-1">
            <div className="h-3 w-24 bg-muted animate-pulse rounded" />
            <div className="flex gap-1">
              <div className="h-6 w-16 bg-muted animate-pulse rounded" />
              <div className="h-6 w-16 bg-muted animate-pulse rounded" />
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
