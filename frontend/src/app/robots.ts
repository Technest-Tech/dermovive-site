import type { MetadataRoute } from "next";
import { locales } from "@/i18n/routing";
import { SITE_URL } from "@/lib/seo";

/** Allow crawling of the storefront; keep the internal design-system page out. */
export default function robots(): MetadataRoute.Robots {
  return {
    rules: {
      userAgent: "*",
      allow: "/",
      disallow: locales.map((locale) => `/${locale}/design-system`),
    },
    sitemap: `${SITE_URL}/sitemap.xml`,
    host: SITE_URL,
  };
}
