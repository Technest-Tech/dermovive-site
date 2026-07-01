/**
 * SEO helpers: the canonical site origin and per-locale `hreflang` alternates.
 * Every route is locale-prefixed (`/en`, `/ar`, `/fr`), so alternates are a
 * mechanical `${SITE_URL}/${locale}${path}` mapping plus an `x-default`.
 */
import type { Metadata } from "next";
import { defaultLocale, locales } from "@/i18n/routing";

/** Absolute public origin (no trailing slash). Configure per environment. */
export const SITE_URL = (
  process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"
).replace(/\/$/, "");

/** Absolute URL for a locale + locale-agnostic path (e.g. "/products/foo"). */
export function localeUrl(locale: string, path = "/"): string {
  const clean = path === "/" ? "" : path.startsWith("/") ? path : `/${path}`;
  return `${SITE_URL}/${locale}${clean}`;
}

/**
 * `alternates` block for `generateMetadata`: a canonical for the active locale
 * plus `hreflang` entries for every locale and `x-default`.
 */
export function alternatesFor(locale: string, path = "/"): Metadata["alternates"] {
  const languages: Record<string, string> = {};
  for (const l of locales) languages[l] = localeUrl(l, path);
  languages["x-default"] = localeUrl(defaultLocale, path);

  return { canonical: localeUrl(locale, path), languages };
}
