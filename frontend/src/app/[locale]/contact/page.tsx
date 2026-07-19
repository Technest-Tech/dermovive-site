import type { Metadata } from "next";
import { getTranslations, setRequestLocale } from "next-intl/server";
import { Mail, Phone, MapPin } from "lucide-react";
import { Breadcrumbs } from "@/components/catalog/Breadcrumbs";
import { ContactForm } from "@/components/contact/ContactForm";
import { CmsContent } from "@/components/content/CmsContent";
import { getPage, getSettings } from "@/lib/queries";
import { alternatesFor } from "@/lib/seo";

type Params = Promise<{ locale: string }>;

export async function generateMetadata({
  params,
}: {
  params: Params;
}): Promise<Metadata> {
  const { locale } = await params;
  const [page, t] = await Promise.all([
    getPage("contact", locale),
    getTranslations({ locale, namespace: "contact" }),
  ]);
  return {
    title: page?.meta.title ?? page?.title ?? t("eyebrow"),
    description: page?.meta.description ?? t("intro"),
    alternates: alternatesFor(locale, "/contact"),
  };
}

export default async function ContactPage({ params }: { params: Params }) {
  const { locale } = await params;
  setRequestLocale(locale);

  const [page, settings, t, tn] = await Promise.all([
    getPage("contact", locale),
    getSettings(locale),
    getTranslations("contact"),
    getTranslations("nav"),
  ]);

  const title = page?.title ?? tn("contact");
  const details = [
    settings?.contact_email && {
      icon: Mail,
      label: t("email"),
      value: settings.contact_email,
      href: `mailto:${settings.contact_email}`,
    },
    settings?.contact_phone && {
      icon: Phone,
      label: t("phone"),
      value: settings.contact_phone,
      href: `tel:${settings.contact_phone.replace(/\s+/g, "")}`,
      ltr: true,
    },
    settings?.address && {
      icon: MapPin,
      label: t("address"),
      value: settings.address,
    },
  ].filter(Boolean) as {
    icon: typeof Mail;
    label: string;
    value: string;
    href?: string;
    ltr?: boolean;
  }[];

  return (
    <div className="container-page py-10 sm:py-14">
      <Breadcrumbs items={[{ href: "/", label: tn("home") }, { label: title }]} />

      <header className="mt-8 max-w-2xl">
        <span className="eyebrow">{t("eyebrow")}</span>
        <h1 className="mt-3 text-4xl sm:text-5xl">{title}</h1>
        {page?.body ? (
          <CmsContent html={page.body} className="mt-5" />
        ) : (
          <p className="mt-5 text-lg leading-relaxed text-muted">{t("intro")}</p>
        )}
      </header>

      <div className="mt-12 grid gap-10 lg:grid-cols-[1fr_1.4fr] lg:gap-16">
        {/* Details */}
        <div>
          <h2 className="text-xl text-teal-800">{t("detailsTitle")}</h2>
          <ul className="mt-6 space-y-6">
            {details.map((item) => (
              <li key={item.label} className="flex items-start gap-4">
                <span className="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-coral-100 text-coral-600">
                  <item.icon className="h-5 w-5" />
                </span>
                <div>
                  <p className="text-xs font-semibold uppercase tracking-wider text-muted">
                    {item.label}
                  </p>
                  {item.href ? (
                    <a
                      href={item.href}
                      dir={item.ltr ? "ltr" : undefined}
                      className="mt-1 block text-teal-800 hover:text-coral-600 rtl:text-right"
                    >
                      {item.value}
                    </a>
                  ) : (
                    <p className="mt-1 text-teal-800">{item.value}</p>
                  )}
                </div>
              </li>
            ))}
          </ul>
        </div>

        {/* Contact form */}
        <div className="rounded-card border border-teal-700/10 bg-white/60 p-6 sm:p-8">
          <h2 className="text-xl text-teal-800">{t("form.title")}</h2>
          <p className="mt-2 text-sm text-muted">{t("form.note")}</p>
          <ContactForm
            accessKey={process.env.NEXT_PUBLIC_WEB3FORMS_ACCESS_KEY}
            copy={{
              name: t("form.name"),
              email: t("form.email"),
              subject: t("form.subject"),
              message: t("form.message"),
              submit: t("form.submit"),
              sending: t("form.sending"),
              success: t("form.success"),
              error: t("form.error"),
              unavailable: t("form.unavailable"),
            }}
          />
        </div>
      </div>
    </div>
  );
}
