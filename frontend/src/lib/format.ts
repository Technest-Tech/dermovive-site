/**
 * Presentation helpers: locale-aware price formatting and resolving CMS link
 * targets (hero slides) to storefront routes.
 */
import type { HeroSlide } from "./types";

/** Format a numeric price as a localised currency string (e.g. "$32.00", "٣٢٫٠٠ US$"). */
export function formatPrice(
  amount: number | null | undefined,
  currency: string,
  locale: string,
): string | null {
  if (amount == null) return null;
  try {
    return new Intl.NumberFormat(locale, {
      style: "currency",
      currency,
    }).format(amount);
  } catch {
    return `${amount.toFixed(2)} ${currency}`;
  }
}

/** Discount percentage when a compare-at price is set and higher than the price. */
export function discountPercent(
  price: number | null | undefined,
  compareAt: number | null | undefined,
): number | null {
  if (price == null || compareAt == null || compareAt <= price) return null;
  return Math.round(((compareAt - price) / compareAt) * 100);
}

/**
 * Resolve a hero slide's link to a destination. `external` true means the
 * target is an absolute URL and should use a plain anchor; otherwise it's an
 * internal, locale-prefixed route for the next-intl `<Link>`.
 */
export function resolveHeroLink(link: HeroSlide["link"]): {
  href: string;
  external: boolean;
} {
  const target = link?.target ?? "";

  switch (link?.type) {
    case "product":
      return { href: `/products/${target}`, external: false };
    case "category":
      return { href: `/categories/${target}`, external: false };
    case "url":
      return { href: target || "/", external: /^https?:\/\//i.test(target) };
    default:
      return { href: "/", external: false };
  }
}
