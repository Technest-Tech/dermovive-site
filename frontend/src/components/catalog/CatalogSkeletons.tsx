import { Skeleton } from "@/components/ui/Skeleton";

/** A single product-card placeholder (image + title + price). */
function ProductCardSkeleton() {
  return (
    <div>
      <Skeleton className="aspect-[4/5] w-full rounded-card" />
      <Skeleton className="mt-3.5 h-3 w-1/3" />
      <Skeleton className="mt-2 h-4 w-3/4" />
      <Skeleton className="mt-2 h-4 w-1/4" />
    </div>
  );
}

/** A responsive grid of product-card placeholders, matching `ProductGrid`. */
export function ProductGridSkeleton({ count = 8 }: { count?: number }) {
  return (
    <div className="grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-3 lg:grid-cols-4">
      {Array.from({ length: count }).map((_, index) => (
        <ProductCardSkeleton key={index} />
      ))}
    </div>
  );
}
