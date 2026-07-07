import { cn } from "@/lib/utils";

/** A soft, pulsing placeholder block used to build loading skeletons. */
export function Skeleton({ className }: { className?: string }) {
  return (
    <div
      aria-hidden
      className={cn("animate-pulse rounded-md bg-teal-700/8", className)}
    />
  );
}
