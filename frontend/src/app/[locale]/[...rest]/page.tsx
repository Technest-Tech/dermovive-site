import { notFound } from "next/navigation";
import { setRequestLocale } from "next-intl/server";

/**
 * Catch-all for unmatched paths under a locale (e.g. /ar/nope). Next only routes
 * `notFound()` from *matched* routes to `[locale]/not-found.tsx`; unmatched URLs
 * would otherwise hit the default root 404 (no locale, no chrome). This funnels
 * them into the localized not-found. More specific routes always win over this.
 */
export default async function CatchAllNotFound({
  params,
}: {
  params: Promise<{ locale: string; rest: string[] }>;
}) {
  const { locale } = await params;
  setRequestLocale(locale);
  notFound();
}
