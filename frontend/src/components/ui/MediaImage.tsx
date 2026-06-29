import Image from "next/image";
import { cn } from "@/lib/utils";

/**
 * Renders a remote API image with `next/image` when a URL is present, and an
 * elegant branded gradient placeholder otherwise (no product imagery is seeded
 * yet). Always fills its nearest positioned ancestor — wrap it in a `relative`
 * box that sets the aspect ratio and `overflow-hidden`.
 */

// Literal class strings (Tailwind v4 scans these; dynamic `from-${x}` would not be detected).
const gradients = [
  "from-coral-100 via-cream to-teal-100",
  "from-teal-100 via-cream to-coral-100",
  "from-sand via-cream to-coral-100",
  "from-coral-200 via-sand to-teal-100",
] as const;

function seededIndex(seed: string, mod: number): number {
  let hash = 0;
  for (let i = 0; i < seed.length; i++) {
    hash = (hash * 31 + seed.charCodeAt(i)) >>> 0;
  }
  return hash % mod;
}

type MediaImageProps = {
  src?: string | null;
  alt: string;
  sizes?: string;
  priority?: boolean;
  className?: string;
  /** Seeds the gradient placeholder so cards in a row look varied but stable. */
  seed?: string;
};

export function MediaImage({
  src,
  alt,
  sizes = "100vw",
  priority,
  className,
  seed,
}: MediaImageProps) {
  if (src) {
    return (
      <Image
        src={src}
        alt={alt}
        fill
        sizes={sizes}
        priority={priority}
        className={cn("object-cover", className)}
      />
    );
  }

  const gradient = gradients[seededIndex(seed || alt, gradients.length)];

  return (
    <div
      aria-hidden
      className={cn(
        "grid h-full w-full place-items-center bg-gradient-to-br",
        gradient,
        className,
      )}
    >
      <span className="select-none font-display text-6xl text-teal-700/15">D</span>
    </div>
  );
}
