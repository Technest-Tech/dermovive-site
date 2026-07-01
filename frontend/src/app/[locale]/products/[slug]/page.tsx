import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import { Breadcrumbs } from "@/components/catalog/Breadcrumbs";
import { ProductGallery } from "@/components/product/ProductGallery";
import { ProductBuyBox } from "@/components/product/ProductBuyBox";
import { ProductTabs } from "@/components/product/ProductTabs";
import { ProductRail } from "@/components/home/ProductRail";
import { getProduct } from "@/lib/queries";
import { alternatesFor, localeUrl } from "@/lib/seo";
import type { ProductDetail } from "@/lib/types";

type Params = Promise<{ locale: string; slug: string }>;

export async function generateMetadata({
  params,
}: {
  params: Params;
}): Promise<Metadata> {
  const { locale, slug } = await params;
  const product = await getProduct(slug, locale);
  if (!product) return {};

  const title = product.meta.title ?? product.name;
  const description =
    product.meta.description ?? product.short_description ?? undefined;

  return {
    title,
    description,
    alternates: alternatesFor(locale, `/products/${slug}`),
    openGraph: {
      title,
      description,
      type: "website",
      url: localeUrl(locale, `/products/${slug}`),
      images: product.gallery.map((image) => image.original),
    },
  };
}

/** Product schema.org JSON-LD for rich results. */
function productJsonLd(product: ProductDetail, url: string) {
  return {
    "@context": "https://schema.org",
    "@type": "Product",
    name: product.name,
    description: product.meta.description ?? product.short_description ?? undefined,
    sku: product.sku ?? undefined,
    category: product.primary_category?.name,
    image: product.gallery.map((image) => image.original),
    brand: { "@type": "Brand", name: "Dermovive Pharma" },
    ...(product.price != null && {
      offers: {
        "@type": "Offer",
        price: product.price,
        priceCurrency: product.currency,
        availability: "https://schema.org/InStock",
        url,
      },
    }),
  };
}

export default async function ProductPage({ params }: { params: Params }) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const product = await getProduct(slug, locale);
  if (!product) notFound();

  const t = await getTranslations("product");
  const tc = await getTranslations("catalog");

  const category = product.primary_category;
  const crumbs = [
    { href: "/", label: tc("breadcrumbHome") },
    { href: "/products", label: tc("allProducts") },
    ...(category
      ? [{ href: `/categories/${category.slug}`, label: category.name }]
      : []),
    { label: product.name },
  ];

  const jsonLd = productJsonLd(product, localeUrl(locale, `/products/${slug}`));

  return (
    <>
      <div className="container-page py-10 sm:py-14">
        <Breadcrumbs items={crumbs} />

        <div className="mt-6 grid gap-10 lg:grid-cols-2 lg:gap-14">
          <ProductGallery
            images={product.gallery}
            alt={product.name}
            seed={product.slug}
          />

          <div>
            {category && (
              <Link
                href={`/categories/${category.slug}`}
                className="text-sm font-medium uppercase tracking-wider text-coral-600 hover:text-coral-700"
              >
                {category.name}
              </Link>
            )}

            <h1 className="mt-2 text-4xl sm:text-5xl">{product.name}</h1>

            {product.badges.length > 0 && (
              <div className="mt-4 flex flex-wrap gap-2">
                {product.badges.map((badge) => (
                  <span
                    key={badge.value}
                    className="rounded-pill bg-teal-700/8 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-800"
                  >
                    {t(`badge.${badge.value}`)}
                  </span>
                ))}
              </div>
            )}

            {product.short_description && (
              <p className="mt-5 text-lg leading-relaxed text-muted">
                {product.short_description}
              </p>
            )}

            <div className="mt-8">
              <ProductBuyBox
                basePrice={product.price}
                compareAt={product.compare_at_price}
                currency={product.currency}
                variants={product.variants}
              />
            </div>

            {product.tags.length > 0 && (
              <div className="mt-8 border-t border-teal-700/10 pt-6">
                <p className="text-xs font-semibold uppercase tracking-wider text-muted">
                  {t("tagsLabel")}
                </p>
                <div className="mt-3 flex flex-wrap gap-2">
                  {product.tags.map((tag) => (
                    <Link
                      key={tag.id}
                      href={`/products?tag=${tag.slug}`}
                      className="rounded-pill border border-teal-700/15 px-3 py-1.5 text-sm text-teal-800 transition-colors hover:border-coral-400 hover:text-coral-700"
                    >
                      {tag.name}
                    </Link>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>

        {product.description && (
          <section className="mt-16 max-w-3xl">
            <h2 className="text-2xl sm:text-3xl">{t("about")}</h2>
            <p className="mt-4 text-lg leading-relaxed text-muted">
              {product.description}
            </p>
          </section>
        )}

        <section className="mt-12">
          <ProductTabs
            benefits={product.benefits}
            ingredients={product.ingredients}
            howToUse={product.how_to_use}
          />
        </section>
      </div>

      {product.related.length > 0 && (
        <ProductRail
          eyebrow={t("related.eyebrow")}
          title={t("related.title")}
          products={product.related}
          viewAllHref={category ? `/categories/${category.slug}` : "/products"}
        />
      )}

      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
    </>
  );
}
