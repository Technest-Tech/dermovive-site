import { useTranslations } from "next-intl";
import { Mail, Phone, MapPin } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { Logo } from "./Logo";
import { WhatsAppIcon } from "./WhatsAppButton";
import { buttonClasses } from "@/components/ui/Button";
import {
  CONTACT_EMAIL,
  CONTACT_PHONE,
  FACEBOOK_URL,
  TIKTOK_URL,
  digitsOnlyPhone,
} from "@/lib/contact";
import type { Settings } from "@/lib/types";

const exploreLinks = [
  { key: "home", href: "/" },
  { key: "products", href: "/products" },
  { key: "contact", href: "/contact" },
] as const;

function FacebookIcon({ className }: { className?: string }) {
  return (
    <svg aria-hidden="true" className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.974h-1.513c-1.49 0-1.956.931-1.956 1.887v2.26h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073Z" />
    </svg>
  );
}

function TikTokIcon({ className }: { className?: string }) {
  return (
    <svg aria-hidden="true" className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.12 2.7 1.63 4.24 1.8V10c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.72-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07Z" />
    </svg>
  );
}

export function Footer({ settings }: { settings: Settings | null }) {
  const t = useTranslations("footer");
  const tn = useTranslations("nav");
  const year = String(new Date().getFullYear());

  const email = settings?.contact_email || CONTACT_EMAIL;
  const phone = settings?.contact_phone || CONTACT_PHONE;
  const address = settings?.address;
  const facebookUrl = settings?.facebook_url || FACEBOOK_URL;
  const tiktokUrl = settings?.tiktok_url || TIKTOK_URL;
  const whatsappNumber =
    settings?.whatsapp_number || settings?.contact_phone || CONTACT_PHONE;
  const hasContact = Boolean(email || phone || address);
  const socialLinks = [
    { label: "Facebook", href: facebookUrl, icon: FacebookIcon },
    { label: "TikTok", href: tiktokUrl, icon: TikTokIcon },
    {
      label: "WhatsApp",
      href: `https://wa.me/${digitsOnlyPhone(whatsappNumber)}`,
      icon: WhatsAppIcon,
    },
  ];

  return (
    <footer id="contact" className="mt-24 bg-teal-800 text-teal-100">
      {/* Newsletter band */}
      <div className="container-page py-14">
        <div className="grid items-center gap-8 rounded-card bg-teal-700/40 p-8 ring-1 ring-cream/10 md:grid-cols-2 md:p-10">
          <div>
            <h3 className="text-2xl text-cream">{t("newsletter.title")}</h3>
            <p className="mt-2 max-w-md text-sm text-teal-100/80">
              {t("newsletter.subtitle")}
            </p>
          </div>
          <form className="flex w-full gap-2" aria-label={t("newsletter.title")}>
            <input
              type="email"
              required
              placeholder={t("newsletter.placeholder")}
              className="h-11 w-full rounded-pill bg-cream/95 px-5 text-sm text-ink placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-coral-400"
            />
            <button type="submit" className={buttonClasses({ size: "md" })}>
              {t("newsletter.cta")}
            </button>
          </form>
        </div>
      </div>

      {/* Columns */}
      <div className="container-page grid gap-10 border-t border-cream/10 py-12 sm:grid-cols-2 lg:grid-cols-4">
        <div className="sm:col-span-2">
          <Logo className="brightness-0 invert" />
          <p className="mt-4 max-w-sm text-sm leading-relaxed text-teal-100/75">
            {t("tagline")}
          </p>
          <div className="mt-5 flex gap-3">
            {socialLinks.map(({ label, href, icon: Icon }) => (
              <a
                key={label}
                href={href}
                target="_blank"
                rel="noreferrer noopener"
                aria-label={label}
                title={label}
                className="grid h-10 w-10 place-items-center rounded-full bg-cream/10 text-cream transition duration-200 hover:-translate-y-0.5 hover:bg-coral-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coral-300"
              >
                <Icon className="h-4.5 w-4.5" />
              </a>
            ))}
          </div>
        </div>

        <div>
          <h4 className="text-sm font-semibold uppercase tracking-wider text-cream">
            {t("explore")}
          </h4>
          <ul className="mt-4 space-y-3 text-sm">
            {exploreLinks.map((link) => (
              <li key={link.key}>
                <Link href={link.href} className="text-teal-100/75 hover:text-coral-300">
                  {tn(link.key)}
                </Link>
              </li>
            ))}
          </ul>
        </div>

        <div>
          <h4 className="text-sm font-semibold uppercase tracking-wider text-cream">
            {t("contact")}
          </h4>
          {hasContact ? (
            <ul className="mt-4 space-y-3 text-sm text-teal-100/75">
              {email && (
                <li>
                  <a
                    href={`mailto:${email}`}
                    className="inline-flex items-center gap-2 hover:text-coral-300"
                  >
                    <Mail className="h-4 w-4 shrink-0" />
                    {email}
                  </a>
                </li>
              )}
              {phone && (
                <li>
                  <a
                    href={`tel:+${digitsOnlyPhone(phone)}`}
                    dir="ltr"
                    className="inline-flex items-center gap-2 hover:text-coral-300 rtl:flex-row-reverse"
                  >
                    <Phone className="h-4 w-4 shrink-0" />
                    {phone}
                  </a>
                </li>
              )}
              {address && (
                <li className="inline-flex items-center gap-2">
                  <MapPin className="h-4 w-4 shrink-0" />
                  {address}
                </li>
              )}
            </ul>
          ) : (
            <p className="mt-4 text-sm text-teal-100/75">{t("tagline")}</p>
          )}
        </div>
      </div>

      <div className="border-t border-cream/10">
        <div className="container-page flex flex-col items-center justify-between gap-2 py-5 text-xs text-teal-100/60 sm:flex-row">
          <p>{t("rights", { year })}</p>
          <Link href="/design-system" className="hover:text-coral-300">
            Design System
          </Link>
        </div>
      </div>
    </footer>
  );
}
