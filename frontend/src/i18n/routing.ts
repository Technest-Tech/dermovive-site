import { defineRouting } from "next-intl/routing";

/**
 * Locale configuration — keep in sync with the backend's
 * config/dermovive.php `locales`.
 */
export const locales = ["en", "ar", "fr"] as const;
export type Locale = (typeof locales)[number];

export const defaultLocale: Locale = "en";

/** Locales rendered right-to-left. */
export const rtlLocales: Locale[] = ["ar"];

export function isRtl(locale: string): boolean {
  return rtlLocales.includes(locale as Locale);
}

export function dirFor(locale: string): "rtl" | "ltr" {
  return isRtl(locale) ? "rtl" : "ltr";
}

export const localeLabels: Record<Locale, string> = {
  en: "English",
  ar: "العربية",
  fr: "Français",
};

export const routing = defineRouting({
  locales,
  defaultLocale,
  // Every URL carries its locale prefix (/en, /ar, /fr) for clarity + SEO.
  localePrefix: "always",
});
