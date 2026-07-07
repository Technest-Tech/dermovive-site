import { Skeleton } from "@/components/ui/Skeleton";

export default function Loading() {
  return (
    <div className="container-page py-10 sm:py-14">
      <Skeleton className="h-4 w-64 max-w-full" />

      <div className="mt-6 grid gap-10 lg:grid-cols-2 lg:gap-14">
        <Skeleton className="aspect-[4/5] w-full rounded-card" />

        <div>
          <Skeleton className="h-3 w-20" />
          <Skeleton className="mt-3 h-11 w-3/4" />
          <Skeleton className="mt-5 h-5 w-full" />
          <Skeleton className="mt-2 h-5 w-2/3" />

          <Skeleton className="mt-8 h-9 w-32" />
          <div className="mt-6 flex gap-2">
            <Skeleton className="h-10 w-20 rounded-pill" />
            <Skeleton className="h-10 w-20 rounded-pill" />
          </div>

          <Skeleton className="mt-6 h-13 w-full rounded-pill" />
          <Skeleton className="mt-3 h-13 w-full rounded-pill" />
        </div>
      </div>

      <Skeleton className="mt-16 h-8 w-48" />
      <Skeleton className="mt-4 h-5 w-full max-w-2xl" />
    </div>
  );
}
