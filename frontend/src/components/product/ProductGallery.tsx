"use client";

import { useState } from "react";
import { useTranslations } from "next-intl";
import { MediaImage } from "@/components/ui/MediaImage";
import { cn } from "@/lib/utils";
import type { MediaImageUrls } from "@/lib/types";

/**
 * Product gallery: a large primary image with a thumbnail strip. Falls back to
 * the branded placeholder when no imagery is seeded (current state), and hides
 * the strip when there is only one image.
 */
export function ProductGallery({
  images,
  alt,
  seed,
}: {
  images: MediaImageUrls[];
  alt: string;
  seed: string;
}) {
  const t = useTranslations("product");
  const [active, setActive] = useState(0);
  const main = images[active];

  return (
    <div className="space-y-4">
      <div className="relative aspect-[4/5] overflow-hidden rounded-card bg-sand ring-1 ring-teal-700/5">
        <MediaImage
          src={main?.preview ?? main?.original}
          alt={alt}
          seed={seed}
          priority
          sizes="(max-width: 1024px) 100vw, 40rem"
        />
      </div>

      {images.length > 1 && (
        <div className="flex flex-wrap gap-3">
          {images.map((image, index) => (
            <button
              key={index}
              type="button"
              onClick={() => setActive(index)}
              aria-label={t("gallery.view", { n: index + 1 })}
              aria-pressed={index === active}
              className={cn(
                "relative aspect-square w-20 overflow-hidden rounded-xl ring-1 transition-shadow",
                index === active
                  ? "ring-2 ring-coral-500"
                  : "ring-teal-700/10 hover:ring-teal-700/30",
              )}
            >
              <MediaImage
                src={image.thumb ?? image.original}
                alt=""
                seed={`${seed}-${index}`}
                sizes="5rem"
              />
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
