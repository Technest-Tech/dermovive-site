import { useTranslations } from "next-intl";
import { setRequestLocale } from "next-intl/server";
import { FlaskConical, Leaf, ShieldCheck, Sparkles, ArrowRight } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { FadeIn } from "@/components/ui/FadeIn";

const categoryTeasers = [
  { label: "Cleansers", from: "from-coral-100", to: "to-coral-200" },
  { label: "Serums", from: "from-teal-100", to: "to-teal-200" },
  { label: "Moisturisers", from: "from-sand", to: "to-coral-100" },
  { label: "Sun Care", from: "from-teal-100", to: "to-sand" },
];

const trustItems = [
  { icon: FlaskConical, label: "Pharmacist formulated" },
  { icon: ShieldCheck, label: "Dermatologically tested" },
  { icon: Leaf, label: "Clean & cruelty-free" },
  { icon: Sparkles, label: "Made to be loved" },
];

export default async function HomePage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  setRequestLocale(locale);
  return (
    <>
      <Hero />
      <TrustBar />
      <Categories />
      <Story />
      <ComingSoon />
    </>
  );
}

function Hero() {
  const t = useTranslations("home.hero");
  return (
    <section className="relative overflow-hidden">
      {/* Decorative gradient orbs */}
      <div className="pointer-events-none absolute -top-32 -end-24 h-96 w-96 rounded-full bg-coral-200/50 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-40 -start-24 h-96 w-96 rounded-full bg-teal-200/40 blur-3xl" />

      <div className="container-page relative grid items-center gap-12 py-16 lg:grid-cols-2 lg:py-24">
        <FadeIn onScroll={false}>
          <span className="eyebrow">{t("eyebrow")}</span>
          <h1 className="mt-4 text-5xl leading-[1.05] sm:text-6xl lg:text-7xl">
            {t("title")}
          </h1>
          <p className="mt-6 max-w-md text-lg leading-relaxed text-muted">
            {t("subtitle")}
          </p>
          <div className="mt-9 flex flex-wrap items-center gap-3">
            <Link href="/#collection" className={buttonClasses({ size: "lg" })}>
              {t("cta")}
              <ArrowRight className="h-4 w-4 rtl:rotate-180" />
            </Link>
            <Link
              href="/#story"
              className={buttonClasses({ variant: "outline", size: "lg" })}
            >
              {t("secondaryCta")}
            </Link>
          </div>
        </FadeIn>

        <FadeIn onScroll={false} delay={0.15}>
          <HeroVisual />
        </FadeIn>
      </div>
    </section>
  );
}

function HeroVisual() {
  return (
    <div className="relative mx-auto aspect-[4/5] w-full max-w-md">
      <div className="absolute inset-0 rounded-[2.5rem] bg-gradient-to-br from-coral-200 via-cream to-teal-200 shadow-[var(--shadow-card)]" />
      <div className="absolute inset-0 grid place-items-center">
        <span className="font-display text-[14rem] leading-none text-teal-700/15 select-none">
          D
        </span>
      </div>

      {/* Floating glass chips */}
      <div className="absolute start-5 top-8 rounded-2xl bg-white/80 px-4 py-3 shadow-[var(--shadow-soft)] backdrop-blur">
        <p className="text-xs font-semibold text-teal-800">4.9 ★ rating</p>
        <p className="text-[0.7rem] text-muted">Loved by 10k+ skins</p>
      </div>
      <div className="absolute end-4 bottom-10 rounded-2xl bg-teal-700 px-4 py-3 text-cream shadow-[var(--shadow-coral)]">
        <p className="text-xs font-semibold">Hydra-Glow Serum</p>
        <p className="text-[0.7rem] text-teal-100/80">Niacinamide + B5</p>
      </div>
    </div>
  );
}

function TrustBar() {
  return (
    <section className="border-y border-teal-700/10 bg-sand/40">
      <div className="container-page grid grid-cols-2 gap-6 py-7 md:grid-cols-4">
        {trustItems.map(({ icon: Icon, label }) => (
          <div key={label} className="flex items-center gap-3">
            <span className="grid h-10 w-10 place-items-center rounded-full bg-white text-coral-600 shadow-[var(--shadow-soft)]">
              <Icon className="h-5 w-5" />
            </span>
            <span className="text-sm font-medium text-teal-800">{label}</span>
          </div>
        ))}
      </div>
    </section>
  );
}

function Categories() {
  const t = useTranslations("home.featured");
  const tc = useTranslations("common");
  return (
    <section id="categories" className="container-page scroll-mt-24 py-20">
      <FadeIn className="flex items-end justify-between gap-6">
        <div>
          <span className="eyebrow">{t("eyebrow")}</span>
          <h2 className="mt-3 text-4xl sm:text-5xl">{t("title")}</h2>
          <p className="mt-3 max-w-md text-muted">{t("body")}</p>
        </div>
        <Link
          href="/#collection"
          className="hidden shrink-0 items-center gap-1 text-sm font-medium text-coral-600 hover:text-coral-700 sm:inline-flex"
        >
          {tc("viewAll")} <ArrowRight className="h-4 w-4 rtl:rotate-180" />
        </Link>
      </FadeIn>

      <div className="mt-10 grid grid-cols-2 gap-5 lg:grid-cols-4">
        {categoryTeasers.map((cat, i) => (
          <FadeIn key={cat.label} delay={i * 0.08}>
            <div className="group cursor-pointer">
              <div
                className={`aspect-[4/5] rounded-card bg-gradient-to-br ${cat.from} ${cat.to} ring-1 ring-teal-700/5 transition-transform duration-500 group-hover:-translate-y-1 group-hover:shadow-[var(--shadow-card)]`}
              />
              <p className="mt-4 text-center font-display text-xl text-teal-800">
                {cat.label}
              </p>
            </div>
          </FadeIn>
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
          <div className="relative aspect-square w-full max-w-md rounded-[2.5rem] bg-gradient-to-tr from-coral-400/40 via-teal-600 to-teal-700 ring-1 ring-cream/10">
            <div className="absolute inset-0 grid place-items-center">
              <Sparkles className="h-16 w-16 text-coral-200" />
            </div>
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

function ComingSoon() {
  const t = useTranslations("home");
  const tc = useTranslations("common");
  return (
    <section id="collection" className="container-page scroll-mt-24 py-24 text-center">
      <FadeIn className="mx-auto max-w-2xl">
        <span className="eyebrow">{tc("comingSoon")}</span>
        <h2 className="mt-4 text-4xl sm:text-5xl">{tc("explore")}</h2>
        <p className="mx-auto mt-5 max-w-lg text-lg text-muted">
          {t("comingSoon")}
        </p>
      </FadeIn>
    </section>
  );
}
