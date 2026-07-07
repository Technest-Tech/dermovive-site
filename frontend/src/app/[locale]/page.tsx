import type { Metadata } from "next";
import Image from "next/image";
import { useTranslations } from "next-intl";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { FlaskConical, Leaf, ShieldCheck, Sparkles, ArrowRight } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { FadeIn } from "@/components/ui/FadeIn";
import { HeroSlider } from "@/components/home/HeroSlider";
import { ProductRail } from "@/components/home/ProductRail";
import { CategoryTeasers } from "@/components/home/CategoryTeasers";
import { getHome } from "@/lib/queries";
import { alternatesFor } from "@/lib/seo";

const COLLECTION_IMAGE = "/brand/collection-dermo-white.jpg";

export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  return { alternates: alternatesFor(locale, "/") };
}

const trustItems = [
  { icon: FlaskConical, key: "pharmacist" },
  { icon: ShieldCheck, key: "tested" },
  { icon: Leaf, key: "clean" },
  { icon: Sparkles, key: "loved" },
] as const;

export default async function HomePage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  setRequestLocale(locale);

  const home = await getHome(locale);
  const tHome = await getTranslations("home");
  const tRails = await getTranslations("home.rails");

  return (
    <>
      <HeroSlider slides={home?.hero ?? []} />

      <TrustBar />

      {home && home.featured.length > 0 && (
        <div id="collection" className="scroll-mt-24">
          <ProductRail
            eyebrow={tRails("featured.eyebrow")}
            title={tRails("featured.title")}
            subtitle={tRails("featured.subtitle")}
            products={home.featured}
            viewAllHref="/products"
          />
        </div>
      )}

      <section id="categories" className="container-page scroll-mt-24 py-16 sm:py-20">
        <FadeIn>
          <span className="eyebrow">{tHome("featured.eyebrow")}</span>
          <h2 className="mt-3 text-4xl sm:text-5xl">{tHome("featured.title")}</h2>
          <p className="mt-3 max-w-md text-muted">{tHome("featured.body")}</p>
        </FadeIn>
        <CategoryTeasers categories={home?.categories ?? []} />
      </section>

      {home && home.newest.length > 0 && (
        <ProductRail
          eyebrow={tRails("newest.eyebrow")}
          title={tRails("newest.title")}
          subtitle={tRails("newest.subtitle")}
          products={home.newest}
          viewAllHref="/products"
        />
      )}

      <Story />
    </>
  );
}

function TrustBar() {
  const t = useTranslations("home.trust");
  return (
    <section className="border-y border-teal-700/10 bg-sand/40">
      <div className="container-page grid grid-cols-2 gap-6 py-7 md:grid-cols-4">
        {trustItems.map(({ icon: Icon, key }) => (
          <div key={key} className="flex items-center gap-3">
            <span className="grid h-10 w-10 place-items-center rounded-full bg-white text-coral-600 shadow-[var(--shadow-soft)]">
              <Icon className="h-5 w-5" />
            </span>
            <span className="text-sm font-medium text-teal-800">{t(key)}</span>
          </div>
        ))}
      </div>
    </section>
  );
}

function Story() {
  const t = useTranslations("home.story");
  return (
    <section id="story" className="scroll-mt-24 bg-teal-800 py-20 text-teal-50">
      <div className="container-page grid items-center gap-12 lg:grid-cols-2">
        <FadeIn>
          <div className="texture-grain relative aspect-square w-full max-w-md overflow-hidden rounded-[2.5rem] bg-cream ring-1 ring-cream/10">
            <Image
              src={COLLECTION_IMAGE}
              alt="Dermovive skincare collection"
              fill
              sizes="(min-width: 1024px) 448px, 100vw"
              className="object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-teal-950/45 via-transparent to-transparent" />
          </div>
        </FadeIn>
        <FadeIn delay={0.1}>
          <span className="eyebrow text-coral-300">{t("eyebrow")}</span>
          <h2 className="mt-3 text-4xl text-cream sm:text-5xl">{t("title")}</h2>
          <p className="mt-5 max-w-md text-lg leading-relaxed text-teal-100/80">
            {t("body")}
          </p>
          <Link
            href="/#collection"
            className={buttonClasses({ size: "lg", className: "mt-8" })}
          >
            {t("cta")}
            <ArrowRight className="h-4 w-4 rtl:rotate-180" />
          </Link>
        </FadeIn>
      </div>
    </section>
  );
}
