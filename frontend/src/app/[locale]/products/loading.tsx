import { Skeleton } from "@/components/ui/Skeleton";
import { ProductGridSkeleton } from "@/components/catalog/CatalogSkeletons";

export default function Loading() {
  return (
    <div className="container-page py-10 sm:py-14">
      <Skeleton className="h-4 w-24" />
      <Skeleton className="mt-4 h-12 w-72 max-w-full" />
      <Skeleton className="mt-4 h-5 w-96 max-w-full" />

      <Skeleton className="mt-9 h-11 w-full rounded-pill" />
      <Skeleton className="mt-4 h-40 w-full rounded-card" />

      <Skeleton className="mt-8 h-4 w-24" />
      <div className="mt-5">
        <ProductGridSkeleton />
      </div>
    </div>
  );
}
