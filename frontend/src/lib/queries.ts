/**
 * Typed, locale-aware fetchers for the public API. Each one degrades
 * gracefully: on a network/HTTP error it logs and returns a safe empty value
 * so pages still render (with fallbacks) and `next build` never depends on the
 * backend being reachable. Time-based revalidation/ISR is deferred to Phase 6.
 */
import { apiFetch } from "./api";
import type {
  Category,
  CategoryDetail,
  HomePayload,
  PageContent,
  Paginated,
  ProductCard,
  ProductDetail,
  ProductQuery,
  Settings,
  TagGroup,
} from "./types";

type Envelope<T> = { data: T };

/** Empty paginated result so the listing renders (with an empty state) if the API is down. */
const EMPTY_PRODUCTS: Paginated<ProductCard> = {
  data: [],
  meta: { current_page: 1, last_page: 1, per_page: 12, total: 0, from: null, to: null },
  links: { first: null, last: null, prev: null, next: null },
};

export async function getHome(locale: string): Promise<HomePayload | null> {
  try {
    const { data } = await apiFetch<Envelope<HomePayload>>("/home", { locale });
    return data;
  } catch (error) {
    console.error("[api] getHome failed:", error);
    return null;
  }
}

export async function getCategoryTree(locale: string): Promise<Category[]> {
  try {
    const { data } = await apiFetch<Envelope<Category[]>>("/categories", { locale });
    return data;
  } catch (error) {
    console.error("[api] getCategoryTree failed:", error);
    return [];
  }
}

export async function getSettings(locale: string): Promise<Settings | null> {
  try {
    const { data } = await apiFetch<Envelope<Settings>>("/settings", { locale });
    return data;
  } catch (error) {
    console.error("[api] getSettings failed:", error);
    return null;
  }
}

/** A single category with breadcrumbs, children and products. `null` if missing/unreachable. */
export async function getCategory(
  slug: string,
  locale: string,
): Promise<CategoryDetail | null> {
  try {
    const { data } = await apiFetch<Envelope<CategoryDetail>>(
      `/categories/${encodeURIComponent(slug)}`,
      { locale },
    );
    return data;
  } catch (error) {
    console.error(`[api] getCategory(${slug}) failed:`, error);
    return null;
  }
}

/** Paginated, filterable product listing. Returns an empty page on failure. */
export async function getProducts(
  query: ProductQuery,
  locale: string,
): Promise<Paginated<ProductCard>> {
  const search = new URLSearchParams();
  for (const [key, value] of Object.entries(query)) {
    if (value != null && value !== "") search.set(key, value);
  }
  const qs = search.toString();

  try {
    return await apiFetch<Paginated<ProductCard>>(
      `/products${qs ? `?${qs}` : ""}`,
      { locale },
    );
  } catch (error) {
    console.error("[api] getProducts failed:", error);
    return EMPTY_PRODUCTS;
  }
}

/** Filter tags grouped by type (skin type / concern / highlight). Empty on failure. */
export async function getTags(locale: string): Promise<TagGroup[]> {
  try {
    const { data } = await apiFetch<Envelope<TagGroup[]>>("/tags", { locale });
    return data;
  } catch (error) {
    console.error("[api] getTags failed:", error);
    return [];
  }
}

/** Full product detail. `null` if the product is missing or the API is unreachable. */
export async function getProduct(
  slug: string,
  locale: string,
): Promise<ProductDetail | null> {
  try {
    const { data } = await apiFetch<Envelope<ProductDetail>>(
      `/products/${encodeURIComponent(slug)}`,
      { locale },
    );
    return data;
  } catch (error) {
    console.error(`[api] getProduct(${slug}) failed:`, error);
    return null;
  }
}

/** A CMS page (Our Story, Contact…). `null` if missing/unreachable. */
export async function getPage(
  slug: string,
  locale: string,
): Promise<PageContent | null> {
  try {
    const { data } = await apiFetch<Envelope<PageContent>>(
      `/pages/${encodeURIComponent(slug)}`,
      { locale },
    );
    return data;
  } catch (error) {
    console.error(`[api] getPage(${slug}) failed:`, error);
    return null;
  }
}
