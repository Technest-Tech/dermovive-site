import type { Metadata } from "next";
import { Cormorant_Garamond, Manrope, IBM_Plex_Sans_Arabic } from "next/font/google";
import { NextIntlClientProvider, hasLocale } from "next-intl";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { routing, dirFor } from "@/i18n/routing";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { WhatsAppButton } from "@/components/layout/WhatsAppButton";
import { getCategoryTree, getSettings } from "@/lib/queries";
import { SITE_URL } from "@/lib/seo";
import { CONTACT_PHONE } from "@/lib/contact";
import "../globals.css";

const cormorant = Cormorant_Garamond({
  subsets: ["latin"],
  weight: ["500", "600", "700"],
  variable: "--font-cormorant",
  display: "swap",
});

const manrope = Manrope({
  subsets: ["latin"],
  variable: "--font-manrope",
  display: "swap",
});

const arabic = IBM_Plex_Sans_Arabic({
  subsets: ["arabic"],
  weight: ["400", "500", "600", "700"],
  variable: "--font-arabic-sans",
  display: "swap",
});

const fontVars = `${cormorant.variable} ${manrope.variable} ${arabic.variable}`;

export function generateStaticParams() {
  return routing.locales.map((locale) => ({ locale }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "common" });
  return {
    metadataBase: new URL(SITE_URL),
    title: {
      default: t("brand"),
      template: `%s · ${t("brand")}`,
    },
    description: t("tagline"),
  };
}

export default async function LocaleLayout({
  children,
  params,
}: {
  children: React.ReactNode;
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!hasLocale(routing.locales, locale)) {
    notFound();
  }

  // Enable static rendering for this locale.
  setRequestLocale(locale);

  // Shared chrome data — category tree (nav) and site settings (footer).
  const [categories, settings, t] = await Promise.all([
    getCategoryTree(locale),
    getSettings(locale),
    getTranslations({ locale, namespace: "common" }),
  ]);

  return (
    <html lang={locale} dir={dirFor(locale)} className={fontVars}>
      <body className="flex min-h-screen flex-col">
        <NextIntlClientProvider>
          <a
            href="#content"
            className="sr-only focus:not-sr-only focus:absolute focus:z-[100] focus:m-4 focus:rounded-pill focus:bg-teal-800 focus:px-5 focus:py-2 focus:text-sm focus:font-medium focus:text-cream"
          >
            {t("skipToContent")}
          </a>
          <Header categories={categories} />
          <main id="content" className="flex-1">
            {children}
          </main>
          <Footer settings={settings} />
          <WhatsAppButton
            phone={
              settings?.whatsapp_number ||
              settings?.contact_phone ||
              CONTACT_PHONE
            }
          />
        </NextIntlClientProvider>
      </body>
    </html>
  );
}
