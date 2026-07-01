/**
 * Types for the Dermovive public API (`/api/v1`). Translatable fields arrive
 * already resolved to the request locale (set via `Accept-Language`), so they
 * are plain strings here. Mirrors the Laravel API Resources in
 * `backend/app/Http/Resources`.
 */

/** Absolute media URLs (original + any generated conversions). */
export type MediaImageUrls = {
  original: string;
  thumb?: string;
  preview?: string;
};

/** A single image's URLs, or null when the collection is empty. */
export type MediaUrls = MediaImageUrls | null;

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

/** A single filter tag (skin type / concern / highlight), locale-resolved. */
export type Tag = {
  id: number;
  type: string | null;
  type_label: string | null;
  slug: string;
  name: string;
};

/** Tags grouped by type for the catalog filter UI. `type` keys the localised heading. */
export type TagGroup = {
  type: string;
  label: string;
  tags: Tag[];
};

/** `GET /categories/{slug}` — a category with its ancestry, children and products. */
export type CategoryDetail = {
  category: Category;
  breadcrumbs: Category[];
  children: Category[];
  products: ProductCard[];
};

/** Laravel paginator metadata (subset we use). */
export type PaginationMeta = {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
};

export type PaginationLinks = {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
};

/** Paginated list envelope (`GET /products`). */
export type Paginated<T> = {
  data: T[];
  meta: PaginationMeta;
  links: PaginationLinks;
};

/** A purchasable variant (size / shade). `name` is a plain, non-translatable string. */
export type ProductVariant = {
  id: number;
  name: string;
  sku: string | null;
  price: number | null;
  is_default: boolean;
};

/** Full product detail (`GET /products/{slug}`) — extends the card with rich content. */
export type ProductDetail = ProductCard & {
  sku: string | null;
  description: string | null;
  ingredients: string | null;
  benefits: string[];
  how_to_use: string[];
  gallery: MediaImageUrls[];
  variants: ProductVariant[];
  tags: Tag[];
  categories: Category[];
  meta: { title: string | null; description: string | null };
  related: ProductCard[];
};

/** A CMS page (`GET /pages/{slug}`) — `body` is trusted, admin-authored HTML. */
export type PageContent = {
  slug: string;
  title: string;
  body: string | null;
  meta: { title: string | null; description: string | null };
};

/** Supported filters for the product listing — mirrors the API query params. */
export type ProductQuery = {
  category?: string;
  tag?: string;
  q?: string;
  sort?: string;
  featured?: string;
  page?: string;
  per_page?: string;
};
