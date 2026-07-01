import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { ArrowRight } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { Breadcrumbs } from "@/components/catalog/Breadcrumbs";
import { CategoryCard } from "@/components/catalog/CategoryCard";
import { ProductGrid } from "@/components/catalog/ProductGrid";
import { getCategory } from "@/lib/queries";
import { alternatesFor } from "@/lib/seo";

type Params = Promise<{ locale: string; slug: string }>;

export async function generateMetadata({
  params,
}: {
  params: Params;
}): Promise<Metadata> {
  const { locale, slug } = await params;
  const detail = await getCategory(slug, locale);
  if (!detail) return {};
  return {
    title: detail.category.name,
    description: detail.category.description ?? undefined,
    alternates: alternatesFor(locale, `/categories/${slug}`),
  };
}

export default async function CategoryPage({ params }: { params: Params }) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const detail = await getCategory(slug, locale);
  if (!detail) notFound();

  const t = await getTranslations("catalog");
  const { category, breadcrumbs, children, products } = detail;

  const crumbs = [
    { href: "/", label: t("breadcrumbHome") },
    ...breadcrumbs.map((crumb) => ({
      href: `/categories/${crumb.slug}`,
      label: crumb.name,
    })),
    { label: category.name },
  ];

  return (
    <div className="container-page py-10 sm:py-14">
      <Breadcrumbs items={crumbs} />

      <header className="mt-6 max-w-2xl">
        <h1 className="text-4xl sm:text-5xl">{category.name}</h1>
        {category.description && (
          <p className="mt-4 text-lg leading-relaxed text-muted">
            {category.description}
          </p>
        )}
      </header>

      {children.length > 0 && (
        <section className="mt-12">
          <h2 className="text-2xl sm:text-3xl">{t("subcategories")}</h2>
          <div className="mt-6 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
            {children.map((child) => (
              <CategoryCard
                key={child.id}
                category={child}
                count={
                  child.product_count != null
                    ? t("productCount", { count: child.product_count })
                    : undefined
                }
              />
            ))}
          </div>
        </section>
      )}

      <section className="mt-14">
        <div className="flex items-end justify-between gap-4">
          <h2 className="text-2xl sm:text-3xl">{t("products")}</h2>
          {products.length > 0 && (
            <Link
              href={`/products?category=${category.slug}`}
              className="inline-flex shrink-0 items-center gap-1 text-sm font-medium text-coral-600 hover:text-coral-700"
            >
              {t("viewAll", { category: category.name })}
              <ArrowRight className="h-4 w-4 rtl:rotate-180" aria-hidden />
            </Link>
          )}
        </div>

        {products.length > 0 ? (
          <div className="mt-7">
            <ProductGrid products={products} />
          </div>
        ) : (
          <p className="mt-6 text-muted">{t("empty.body")}</p>
        )}
      </section>
    </div>
  );
}
