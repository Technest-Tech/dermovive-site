import { Skeleton } from "@/components/ui/Skeleton";
import { ProductGridSkeleton } from "@/components/catalog/CatalogSkeletons";

export default function Loading() {
  return (
    <div className="container-page py-10 sm:py-14">
      <Skeleton className="h-4 w-56 max-w-full" />
      <Skeleton className="mt-6 h-12 w-80 max-w-full" />
      <Skeleton className="mt-4 h-5 w-full max-w-xl" />

      <div className="mt-12">
        <ProductGridSkeleton count={8} />
      </div>
    </div>
  );
}
