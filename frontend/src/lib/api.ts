/**
 * Thin API client for the Laravel backend (read-only in Phase 1).
 *
 * All catalog fetching in later phases goes through `apiFetch`, which adds
 * the locale header and a consistent base URL. Response caching/revalidation
 * is controlled per-call via the standard fetch `next` options.
 */
import { defaultLocale } from "@/i18n/routing";

export const API_BASE_URL =
  process.env.NEXT_PUBLIC_API_URL ?? "http://127.0.0.1:8000/api/v1";

type ApiFetchOptions = RequestInit & {
  locale?: string;
  /** Next.js fetch cache hints (e.g. { revalidate: 300, tags: ['products'] }). */
  next?: { revalidate?: number | false; tags?: string[] };
};

export async function apiFetch<T>(
  path: string,
  { locale = defaultLocale, headers, ...options }: ApiFetchOptions = {},
): Promise<T> {
  const url = `${API_BASE_URL}${path.startsWith("/") ? path : `/${path}`}`;

  const res = await fetch(url, {
    ...options,
    headers: {
      Accept: "application/json",
      "Accept-Language": locale,
      ...headers,
    },
  });

  if (!res.ok) {
    throw new Error(`API ${res.status} ${res.statusText} for ${url}`);
  }

  return res.json() as Promise<T>;
}
