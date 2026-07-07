import Image from "next/image";
import { cn } from "@/lib/utils";
import { SerumBottle, CreamJar, Sparkle } from "@/components/decor/BrandDecor";

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

  const key = seed || alt;
  const gradient = gradients[seededIndex(key, gradients.length)];
  const Product = seededIndex(key, 2) === 0 ? SerumBottle : CreamJar;

  return (
    <div
      aria-hidden
      className={cn(
        "relative grid h-full w-full place-items-center overflow-hidden bg-gradient-to-br",
        gradient,
        className,
      )}
    >
      {/* dotted texture for depth */}
      <div
        className="absolute inset-0 opacity-[0.35]"
        style={{
          backgroundImage:
            "radial-gradient(rgba(28,74,69,0.14) 1px, transparent 1.4px)",
          backgroundSize: "14px 14px",
        }}
      />
      <Sparkle className="absolute end-[16%] top-[14%] h-6 w-6 text-coral-400/50" />
      <Sparkle className="absolute start-[18%] bottom-[20%] h-4 w-4 text-teal-500/40" />
      <Product className="relative h-[62%] w-auto text-teal-700/25" />
      <span className="absolute bottom-3 select-none font-display text-xs tracking-[0.3em] text-teal-700/30 uppercase">
        Dermovive
      </span>
    </div>
  );
}
