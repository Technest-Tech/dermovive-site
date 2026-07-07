/**
 * ISR + cache-tag configuration for the API-backed pages.
 *
 * Read fetchers pass `next: { revalidate, tags }` so pages refresh in the
 * background every `REVALIDATE_SECONDS` and can be busted on demand via the
 * `/api/revalidate` webhook (see `app/api/revalidate/route.ts`) when the admin
 * changes content. Tags mirror the API resources.
 */
export const REVALIDATE_SECONDS = Number(process.env.REVALIDATE_SECONDS ?? 300);

export const cacheTags = {
  home: "home",
  categories: "categories",
  products: "products",
  filters: "tags",
  settings: "settings",
  pages: "pages",
  category: (slug: string) => `category:${slug}`,
  product: (slug: string) => `product:${slug}`,
  page: (slug: string) => `page:${slug}`,
} as const;
