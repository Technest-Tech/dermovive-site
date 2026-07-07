import { cn } from "@/lib/utils";

/* ==========================================================================
   Brand decorative vectors
   Lightweight, dependency-free SVG artwork used to give the cosmetics UI a
   crafted, editorial feel where product photography is not yet available.
   Every piece uses `currentColor` (or explicit brand tokens) so it can be
   tinted with Tailwind text-* utilities and is purely decorative (aria-hidden).
   ========================================================================== */

type DecorProps = { className?: string };

/** A tall serum / dropper bottle silhouette — the signature hero prop. */
export function SerumBottle({ className }: DecorProps) {
  return (
    <svg viewBox="0 0 120 260" fill="none" aria-hidden className={className}>
      {/* dropper cap */}
      <rect x="46" y="2" width="28" height="34" rx="7" fill="currentColor" opacity="0.9" />
      <rect x="52" y="34" width="16" height="14" rx="3" fill="currentColor" opacity="0.7" />
      {/* shoulders + body */}
      <path
        d="M40 60c0-6 5-11 11-11h18c6 0 11 5 11 11v9c22 10 34 30 34 58v54c0 15-12 27-27 27H33c-15 0-27-12-27-27v-54c0-28 12-48 34-58v-9z"
        fill="currentColor"
        opacity="0.16"
      />
      <path
        d="M40 60c0-6 5-11 11-11h18c6 0 11 5 11 11v9c22 10 34 30 34 58v54c0 15-12 27-27 27H33c-15 0-27-12-27-27v-54c0-28 12-48 34-58v-9z"
        stroke="currentColor"
        strokeWidth="2.5"
        opacity="0.55"
      />
      {/* liquid fill */}
      <path
        d="M12 150c0-16 4-30 12-41 6 9 15 12 24 7 8-4 9-14 20-14 12 0 13 12 24 14 7 1 12-1 16-6 5 9 8 22 8 40v42c0 12-10 22-22 22H34c-12 0-22-10-22-22v-42z"
        fill="currentColor"
        opacity="0.5"
      />
      {/* label */}
      <rect x="26" y="150" width="68" height="40" rx="9" fill="var(--color-cream)" opacity="0.85" />
      <rect x="38" y="163" width="44" height="4" rx="2" fill="currentColor" opacity="0.6" />
      <rect x="44" y="174" width="32" height="4" rx="2" fill="currentColor" opacity="0.4" />
    </svg>
  );
}

/** A botanical sprig — soft organic contrast to the glassware. */
export function LeafSprig({ className }: DecorProps) {
  return (
    <svg viewBox="0 0 160 200" fill="none" aria-hidden className={className}>
      <path d="M80 200V40" stroke="currentColor" strokeWidth="3" strokeLinecap="round" opacity="0.7" />
      {[0, 1, 2, 3].map((i) => {
        const y = 60 + i * 34;
        return (
          <g key={i} opacity={0.85 - i * 0.12}>
            <path
              d={`M80 ${y}c-30-6-52 4-64 24 22 8 46 4 64-10z`}
              fill="currentColor"
              opacity="0.5"
            />
            <path
              d={`M80 ${y - 18}c30-6 52 4 64 24-22 8-46 4-64-10z`}
              fill="currentColor"
              opacity="0.7"
            />
          </g>
        );
      })}
      <circle cx="80" cy="34" r="9" fill="currentColor" />
    </svg>
  );
}

/** A jar / cream pot silhouette. */
export function CreamJar({ className }: DecorProps) {
  return (
    <svg viewBox="0 0 200 200" fill="none" aria-hidden className={className}>
      <rect x="30" y="70" width="140" height="30" rx="12" fill="currentColor" opacity="0.85" />
      <path
        d="M40 98h120v54c0 17-14 30-30 30H70c-17 0-30-13-30-30V98z"
        fill="currentColor"
        opacity="0.2"
      />
      <path
        d="M40 98h120v54c0 17-14 30-30 30H70c-17 0-30-13-30-30V98z"
        stroke="currentColor"
        strokeWidth="2.5"
        opacity="0.5"
      />
      <ellipse cx="100" cy="70" rx="70" ry="14" fill="currentColor" opacity="0.6" />
    </svg>
  );
}

/** A four-point sparkle / shine accent. */
export function Sparkle({ className }: DecorProps) {
  return (
    <svg viewBox="0 0 100 100" fill="none" aria-hidden className={className}>
      <path
        d="M50 4c4 26 16 40 42 46-26 4-38 18-42 46-4-28-16-42-42-46 26-6 38-20 42-46z"
        fill="currentColor"
      />
    </svg>
  );
}

/** A single dew droplet. */
export function Droplet({ className }: DecorProps) {
  return (
    <svg viewBox="0 0 60 80" fill="none" aria-hidden className={className}>
      <path
        d="M30 4C18 26 8 38 8 52a22 22 0 0044 0c0-14-10-26-22-48z"
        fill="currentColor"
      />
      <path
        d="M22 50a8 8 0 006 12"
        stroke="var(--color-cream)"
        strokeWidth="3"
        strokeLinecap="round"
        opacity="0.7"
        fill="none"
      />
    </svg>
  );
}

/** Concentric outline rings — abstract "radiance" motif. */
export function RadianceRings({ className }: DecorProps) {
  return (
    <svg viewBox="0 0 400 400" fill="none" aria-hidden className={cn(className)}>
      {[196, 150, 104, 58].map((r, i) => (
        <circle
          key={r}
          cx="200"
          cy="200"
          r={r}
          stroke="currentColor"
          strokeWidth="1.5"
          strokeDasharray={i % 2 ? "3 10" : undefined}
          opacity={0.5 - i * 0.07}
        />
      ))}
    </svg>
  );
}
