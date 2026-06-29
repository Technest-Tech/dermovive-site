import type { Metadata } from "next";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { ArrowRight } from "lucide-react";
import { Button, buttonClasses } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Design System",
};

const coralScale = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
const tealScale = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];
const neutrals = [
  { name: "cream" },
  { name: "sand" },
  { name: "sand-dark" },
  { name: "ink" },
  { name: "muted" },
];

export default async function DesignSystemPage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  setRequestLocale(locale);
  const t = await getTranslations("designSystem");

  return (
    <div className="container-page py-16">
      <header className="max-w-2xl">
        <span className="eyebrow">Dermovive Pharma</span>
        <h1 className="mt-3 text-5xl">{t("title")}</h1>
        <p className="mt-4 text-lg text-muted">{t("subtitle")}</p>
      </header>

      {/* Colours */}
      <Section title={t("colors")}>
        <p className="mb-3 text-sm font-medium text-teal-700">Coral — primary</p>
        <div className="grid grid-cols-5 gap-2 sm:grid-cols-10">
          {coralScale.map((s) => (
            <Swatch key={s} varName={`--color-coral-${s}`} label={`${s}`} dark={s >= 500} />
          ))}
        </div>

        <p className="mt-8 mb-3 text-sm font-medium text-teal-700">Teal — brand ink</p>
        <div className="grid grid-cols-6 gap-2 sm:grid-cols-11">
          {tealScale.map((s) => (
            <Swatch key={s} varName={`--color-teal-${s}`} label={`${s}`} dark={s >= 400} />
          ))}
        </div>

        <p className="mt-8 mb-3 text-sm font-medium text-teal-700">Neutrals</p>
        <div className="grid grid-cols-5 gap-2">
          {neutrals.map((n) => (
            <Swatch
              key={n.name}
              varName={`--color-${n.name}`}
              label={n.name}
              dark={["ink", "muted", "sand-dark"].includes(n.name)}
            />
          ))}
        </div>
      </Section>

      {/* Typography */}
      <Section title={t("typography")}>
        <div className="space-y-4">
          <p className="eyebrow">Eyebrow · uppercase tracking</p>
          <h1 className="text-6xl">{t("sampleHeading")}</h1>
          <h2 className="text-4xl">{t("sampleHeading")}</h2>
          <h3 className="text-2xl">{t("sampleHeading")}</h3>
          <p className="max-w-2xl text-lg text-muted">{t("sampleBody")}</p>
          <p className="max-w-2xl text-sm text-muted">{t("sampleBody")}</p>
        </div>
      </Section>

      {/* Buttons */}
      <Section title={t("buttons")}>
        <div className="flex flex-wrap items-center gap-3">
          <Button variant="primary">Primary</Button>
          <Button variant="dark">Dark</Button>
          <Button variant="outline">Outline</Button>
          <Button variant="ghost">Ghost</Button>
          <a href="#" className={buttonClasses({ variant: "primary" })}>
            Link button <ArrowRight className="h-4 w-4 rtl:rotate-180" />
          </a>
        </div>
        <div className="mt-5 flex flex-wrap items-center gap-3">
          <Button size="sm">Small</Button>
          <Button size="md">Medium</Button>
          <Button size="lg">Large</Button>
        </div>
      </Section>

      {/* Cards */}
      <Section title={t("cards")}>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <div className="rounded-card bg-white p-2 shadow-[var(--shadow-card)]">
            <div className="aspect-[4/3] rounded-[1rem] bg-gradient-to-br from-coral-100 to-teal-100" />
            <div className="p-4">
              <span className="rounded-pill bg-coral-100 px-2.5 py-1 text-[0.7rem] font-semibold uppercase tracking-wide text-coral-700">
                Bestseller
              </span>
              <h4 className="mt-3 text-xl">Hydra-Glow Serum</h4>
              <p className="mt-1 text-sm text-muted">Niacinamide + Vitamin B5</p>
            </div>
          </div>

          <div className="rounded-card bg-teal-800 p-6 text-cream shadow-[var(--shadow-card)]">
            <h4 className="text-cream">Dark surface</h4>
            <p className="mt-2 text-sm text-teal-100/80">
              Used for the story section and footer.
            </p>
          </div>

          <div className="rounded-card border border-teal-700/15 bg-sand/40 p-6">
            <h4>Soft surface</h4>
            <p className="mt-2 text-sm text-muted">Sand tint with hairline border.</p>
          </div>
        </div>
      </Section>
    </div>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <section className="mt-16 border-t border-teal-700/10 pt-10">
      <h2 className="mb-6 text-2xl">{title}</h2>
      {children}
    </section>
  );
}

function Swatch({
  varName,
  label,
  dark,
}: {
  varName: string;
  label: string;
  dark?: boolean;
}) {
  return (
    <div
      className="flex aspect-square items-end rounded-xl p-1.5 ring-1 ring-inset ring-black/5"
      style={{ backgroundColor: `var(${varName})` }}
    >
      <span
        className={`text-[0.65rem] font-semibold ${dark ? "text-white/90" : "text-teal-900/70"}`}
      >
        {label}
      </span>
    </div>
  );
}
