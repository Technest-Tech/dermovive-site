import type { Metadata } from "next";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { SearchX } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { ProductFilters, type CategoryOption } from "@/components/catalog/ProductFilters";
import { ProductGrid } from "@/components/catalog/ProductGrid";
import { Pagination } from "@/components/catalog/Pagination";
import { getCategoryTree, getProducts, getTags } from "@/lib/queries";
import { alternatesFor } from "@/lib/seo";
import type { Category, ProductQuery } from "@/lib/types";

type Params = Promise<{ locale: string }>;
type SearchParams = Promise<Record<string, string | string[] | undefined>>;

export async function generateMetadata({
  params,
}: {
  params: Params;
}): Promise<Metadata> {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "catalog" });
  return {
    title: t("allProducts"),
    description: t("allProductsSubtitle"),
    alternates: alternatesFor(locale, "/products"),
  };
}

/** First value of a possibly-repeated search param. */
function first(value: string | string[] | undefined): string | undefined {
  const result = Array.isArray(value) ? value[0] : value;
  return result === "" ? undefined : result;
}

/** Flatten the category tree into indented options for the filter select. */
function flattenTree(categories: Category[], depth = 0): CategoryOption[] {
  return categories.flatMap((category) => [
    { slug: category.slug, name: category.name, depth },
    ...(category.children ? flattenTree(category.children, depth + 1) : []),
  ]);
}

export default async function ProductsPage({
  params,
  searchParams,
}: {
  params: Params;
  searchParams: SearchParams;
}) {
  const { locale } = await params;
  setRequestLocale(locale);

  const sp = await searchParams;
  const query: ProductQuery = {
    category: first(sp.category),
    tag: first(sp.tag),
    q: first(sp.q),
    sort: first(sp.sort),
    featured: first(sp.featured),
    per_page: first(sp.per_page),
    page: first(sp.page),
  };

  const t = await getTranslations("catalog");

  const [list, tree, tagGroups] = await Promise.all([
    getProducts(query, locale),
    getCategoryTree(locale),
    getTags(locale),
  ]);

  const categories = flattenTree(tree);

  return (
    <div className="container-page py-10 sm:py-14">
      <header className="max-w-2xl">
        <span className="eyebrow">{t("eyebrow")}</span>
        <h1 className="mt-3 text-4xl sm:text-5xl">{t("allProducts")}</h1>
        <p className="mt-4 text-lg leading-relaxed text-muted">
          {t("allProductsSubtitle")}
        </p>
      </header>

      <div className="mt-9">
        <ProductFilters categories={categories} tagGroups={tagGroups} />
      </div>

      <p className="mt-8 text-sm text-muted">
        {t("results", { count: list.meta.total })}
      </p>

      {list.data.length > 0 ? (
        <>
          <div className="mt-5">
            <ProductGrid products={list.data} />
          </div>
          <div className="mt-14">
            <Pagination meta={list.meta} query={query} locale={locale} />
          </div>
        </>
      ) : (
        <div className="mt-6 grid place-items-center rounded-card border border-teal-700/10 bg-white/50 px-6 py-20 text-center">
          <SearchX className="h-10 w-10 text-teal-700/30" aria-hidden />
          <h2 className="mt-4 text-2xl">{t("empty.title")}</h2>
          <p className="mt-2 max-w-sm text-muted">{t("empty.body")}</p>
          <Link href="/products" className={buttonClasses({ variant: "outline", className: "mt-6" })}>
            {t("empty.reset")}
          </Link>
        </div>
      )}
    </div>
  );
}
