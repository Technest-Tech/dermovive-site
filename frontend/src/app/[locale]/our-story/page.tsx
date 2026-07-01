import type { Metadata } from "next";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { FlaskConical, Leaf, Globe, ShieldCheck, ArrowRight, BadgeCheck } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { FadeIn } from "@/components/ui/FadeIn";
import { Breadcrumbs } from "@/components/catalog/Breadcrumbs";
import { CmsContent } from "@/components/content/CmsContent";
import { getPage } from "@/lib/queries";
import { alternatesFor } from "@/lib/seo";

type Params = Promise<{ locale: string }>;

const values = [
  { icon: FlaskConical, key: "science" },
  { icon: Leaf, key: "clean" },
  { icon: Globe, key: "trilingual" },
  { icon: ShieldCheck, key: "tested" },
] as const;

const certs = ["derm", "cruelty", "vegan", "quality"] as const;

export async function generateMetadata({
  params,
}: {
  params: Params;
}): Promise<Metadata> {
  const { locale } = await params;
  const [page, t] = await Promise.all([
    getPage("our-story", locale),
    getTranslations({ locale, namespace: "about" }),
  ]);
  const title = page?.meta.title ?? page?.title ?? t("eyebrow");
  return {
    title,
    description: page?.meta.description ?? t("ctaBody"),
    alternates: alternatesFor(locale, "/our-story"),
  };
}

export default async function OurStoryPage({ params }: { params: Params }) {
  const { locale } = await params;
  setRequestLocale(locale);

  const [page, t, tn] = await Promise.all([
    getPage("our-story", locale),
    getTranslations("about"),
    getTranslations("nav"),
  ]);

  const title = page?.title ?? tn("about");

  return (
    <div className="container-page py-10 sm:py-14">
      <Breadcrumbs
        items={[{ href: "/", label: tn("home") }, { label: title }]}
      />

      <header className="mt-8 max-w-3xl">
        <span className="eyebrow">{t("eyebrow")}</span>
        <h1 className="mt-3 text-4xl sm:text-5xl">{title}</h1>
        {page?.body && <CmsContent html={page.body} className="mt-6" />}
      </header>

      {/* Values */}
      <section className="mt-16">
        <h2 className="text-2xl sm:text-3xl">{t("valuesTitle")}</h2>
        <div className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {values.map(({ icon: Icon, key }, index) => (
            <FadeIn key={key} delay={index * 0.05}>
              <div className="h-full rounded-card border border-teal-700/10 bg-white/60 p-6">
                <span className="grid h-11 w-11 place-items-center rounded-full bg-coral-100 text-coral-600">
                  <Icon className="h-5 w-5" />
                </span>
                <h3 className="mt-4 text-lg text-teal-800">
                  {t(`values.${key}.title`)}
                </h3>
                <p className="mt-2 text-sm leading-relaxed text-muted">
                  {t(`values.${key}.body`)}
                </p>
              </div>
            </FadeIn>
          ))}
        </div>
      </section>

      {/* Certifications */}
      <section className="mt-14 rounded-card bg-sand/50 p-8 sm:p-10">
        <h2 className="text-center text-2xl">{t("certsTitle")}</h2>
        <div className="mt-6 flex flex-wrap items-center justify-center gap-x-8 gap-y-4">
          {certs.map((key) => (
            <span
              key={key}
              className="inline-flex items-center gap-2 text-sm font-medium text-teal-800"
            >
              <BadgeCheck className="h-5 w-5 text-coral-500" />
              {t(`certs.${key}`)}
            </span>
          ))}
        </div>
      </section>

      {/* CTA */}
      <section className="mt-16 overflow-hidden rounded-card bg-teal-800 px-8 py-14 text-center text-cream sm:px-10">
        <h2 className="text-3xl text-cream sm:text-4xl">{t("ctaTitle")}</h2>
        <p className="mx-auto mt-3 max-w-md text-teal-100/80">{t("ctaBody")}</p>
        <Link
          href="/products"
          className={buttonClasses({ size: "lg", className: "mt-7" })}
        >
          {t("ctaButton")}
          <ArrowRight className="h-4 w-4 rtl:rotate-180" />
        </Link>
      </section>
    </div>
  );
}
