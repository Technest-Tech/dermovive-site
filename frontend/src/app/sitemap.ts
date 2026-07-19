import type { MetadataRoute } from "next";
import { defaultLocale, locales } from "@/i18n/routing";
import { getCategoryTree, getProducts } from "@/lib/queries";
import { localeUrl } from "@/lib/seo";
import type { Category } from "@/lib/types";

/** Collect every category slug from the nested tree. */
function categorySlugs(categories: Category[]): string[] {
  return categories.flatMap((category) => [
    category.slug,
    ...(category.children ? categorySlugs(category.children) : []),
  ]);
}

/**
 * Multilingual sitemap. Slugs are locale-agnostic (generated from the default
 * locale), so we fetch them once and emit each path for every locale via
 * `alternates.languages` (hreflang). Fetchers degrade gracefully, so a sitemap
 * is still produced (static routes only) if the API is unreachable.
 */
export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const [tree, products] = await Promise.all([
    getCategoryTree(defaultLocale),
    getProducts({ per_page: "48" }, defaultLocale),
  ]);

  const staticPaths = ["/", "/products", "/contact"];
  const categoryPaths = categorySlugs(tree).map((slug) => `/categories/${slug}`);
  const productPaths = products.data.map((product) => `/products/${product.slug}`);
  const paths = [...staticPaths, ...categoryPaths, ...productPaths];

  const lastModified = new Date();

  return paths.map((path) => ({
    url: localeUrl(defaultLocale, path),
    lastModified,
    changeFrequency: "weekly",
    priority: path === "/" ? 1 : path.startsWith("/products/") ? 0.7 : 0.8,
    alternates: {
      languages: Object.fromEntries(
        locales.map((locale) => [locale, localeUrl(locale, path)]),
      ),
    },
  }));
}
