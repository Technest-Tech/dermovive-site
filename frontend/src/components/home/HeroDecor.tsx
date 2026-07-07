import {
  SerumBottle,
  LeafSprig,
  CreamJar,
  Sparkle,
  Droplet,
  RadianceRings,
} from "@/components/decor/BrandDecor";

/**
 * Layered decorative artwork for the hero. Fills its positioned ancestor and
 * sits behind the headline. Two modes:
 *  - `solid`  — full mesh background + product silhouettes (no photo case)
 *  - `overlay`— subtle floating accents that read over a supplied photo
 * All layers are aria-hidden and animate via CSS (auto-stilled under
 * prefers-reduced-motion).
 */
export function HeroDecor({ mode = "solid" }: { mode?: "solid" | "overlay" }) {
  if (mode === "overlay") {
    return (
      <div aria-hidden className="pointer-events-none absolute inset-0 overflow-hidden">
        <RadianceRings className="animate-spin-slow absolute -end-24 -top-24 h-[420px] w-[420px] text-cream/25" />
        <Sparkle className="animate-shimmer absolute end-[14%] top-[22%] h-8 w-8 text-coral-200" />
        <Sparkle className="animate-shimmer absolute end-[26%] top-[38%] h-5 w-5 text-cream [animation-delay:1.2s]" />
        <Droplet className="animate-float-y absolute end-[10%] bottom-[24%] h-9 w-9 text-coral-200/80" />
      </div>
    );
  }

  return (
    <div aria-hidden className="pointer-events-none absolute inset-0 overflow-hidden">
      {/* orbiting radiance rings anchored to the product cluster */}
      <RadianceRings className="animate-spin-slow absolute -end-16 top-1/2 h-[560px] w-[560px] -translate-y-1/2 text-cream/25" />

      {/* soft colour blooms */}
      <div className="absolute -start-24 top-8 h-72 w-72 rounded-full bg-coral-400/25 blur-3xl" />
      <div className="absolute end-[22%] -bottom-24 h-96 w-96 rounded-full bg-teal-300/20 blur-3xl" />

      {/* product silhouette cluster (right on desktop, hidden on small screens) */}
      <div className="absolute end-[6%] top-1/2 hidden -translate-y-1/2 md:block">
        <div className="relative h-[300px] w-[300px] lg:h-[360px] lg:w-[360px]">
          <SerumBottle className="animate-float-y absolute start-1/2 top-0 h-[300px] w-auto -translate-x-1/2 text-cream lg:h-[360px]" />
          <CreamJar className="animate-float-y-slow absolute -start-16 bottom-4 h-28 w-28 text-coral-200/90 [animation-delay:0.8s] lg:-start-20 lg:h-32 lg:w-32" />
          <LeafSprig className="animate-drift absolute -end-10 -top-6 h-40 w-32 text-teal-200/80" />
          <Droplet className="animate-float-y absolute -end-4 bottom-16 h-8 w-8 text-coral-200 [animation-delay:1.5s]" />
        </div>
      </div>

      {/* scattered sparkles */}
      <Sparkle className="animate-shimmer absolute start-[8%] bottom-[18%] h-10 w-10 text-coral-300" />
      <Sparkle className="animate-shimmer absolute start-[46%] top-[16%] h-6 w-6 text-cream [animation-delay:0.9s]" />
      <Sparkle className="animate-shimmer absolute end-[40%] bottom-[26%] h-5 w-5 text-teal-200 [animation-delay:1.8s]" />
    </div>
  );
}
