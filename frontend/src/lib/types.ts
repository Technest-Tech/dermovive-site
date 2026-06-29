/**
 * Types for the Dermovive public API (`/api/v1`). Translatable fields arrive
 * already resolved to the request locale (set via `Accept-Language`), so they
 * are plain strings here. Mirrors the Laravel API Resources in
 * `backend/app/Http/Resources`.
 */

/** Absolute media URLs (original + any generated conversions), or null when empty. */
export type MediaUrls = {
  original: string;
  thumb?: string;
  preview?: string;
} | null;

export type ProductBadge = {
  value: "new" | "bestseller" | "limited";
  label: string;
  color: string;
};

export type CategoryRef = {
  id: number;
  slug: string;
  name: string;
};

/** Lightweight product shape used in listings, rails and carousels. */
export type ProductCard = {
  id: number;
  slug: string;
  name: string;
  short_description: string | null;
  price: number | null;
  compare_at_price: number | null;
  currency: string;
  is_featured: boolean;
  badges: ProductBadge[];
  primary_category?: CategoryRef | null;
  image: MediaUrls;
};

export type Category = {
  id: number;
  slug: string;
  name: string;
  description: string | null;
  image: MediaUrls;
  product_count?: number;
  children?: Category[];
};

export type HeroSlide = {
  id: number;
  title: string;
  subtitle: string | null;
  cta_label: string | null;
  link: {
    type: "product" | "category" | "url" | null;
    target: string | null;
  };
  image: MediaUrls;
};

export type HomePayload = {
  hero: HeroSlide[];
  featured: ProductCard[];
  newest: ProductCard[];
  categories: Category[];
};

export type Settings = {
  site_name?: string;
  tagline?: string;
  contact_email?: string;
  contact_phone?: string;
  address?: string;
  [key: string]: string | undefined;
};
