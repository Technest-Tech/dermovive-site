import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { Breadcrumbs } from "@/components/catalog/Breadcrumbs";
import { CmsContent } from "@/components/content/CmsContent";
import { getPage } from "@/lib/queries";
import { alternatesFor } from "@/lib/seo";

type Params = Promise<{ locale: string }>;

/** Shared SEO metadata for a CMS-backed page rendered at /{slug}. */
export async function cmsPageMetadata(
  slug: string,
  params: Params,
): Promise<Metadata> {
  const { locale } = await params;
  const page = await getPage(slug, locale);
  return {
    title: page?.meta.title ?? page?.title,
    description: page?.meta.description ?? undefined,
    alternates: alternatesFor(locale, `/${slug}`),
  };
}

/** Renders a published CMS page (title + rich-text body) by slug, or 404s. */
export async function CmsPage({ slug, params }: { slug: string; params: Params }) {
  const { locale } = await params;
  setRequestLocale(locale);

  const [page, tn] = await Promise.all([
    getPage(slug, locale),
    getTranslations("nav"),
  ]);

  if (!page) notFound();

  return (
    <div className="container-page py-10 sm:py-14">
      <Breadcrumbs
        items={[{ href: "/", label: tn("home") }, { label: page.title }]}
      />

      <header className="mt-8 max-w-3xl">
        <h1 className="mt-3 text-4xl sm:text-5xl">{page.title}</h1>
      </header>

      {page.body && <CmsContent html={page.body} className="mt-8 max-w-3xl" />}
    </div>
  );
}
