import { useTranslations } from "next-intl";
import { Send, MessageCircle, Mail, Phone, MapPin } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { Logo } from "./Logo";
import { buttonClasses } from "@/components/ui/Button";
import type { Settings } from "@/lib/types";

const exploreLinks = [
  { key: "products", href: "/products" },
  { key: "about", href: "/#about" },
  { key: "contact", href: "/contact" },
] as const;

export function Footer({ settings }: { settings: Settings | null }) {
  const t = useTranslations("footer");
  const tn = useTranslations("nav");
  const year = String(new Date().getFullYear());

  const email = settings?.contact_email;
  const phone = settings?.contact_phone;
  const address = settings?.address;
  const hasContact = Boolean(email || phone || address);

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
            {[Send, MessageCircle, Mail].map((Icon, i) => (
              <span
                key={i}
                className="grid h-9 w-9 place-items-center rounded-full bg-cream/10 text-cream transition-colors hover:bg-coral-500"
              >
                <Icon className="h-4 w-4" />
              </span>
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
                    href={`tel:${phone.replace(/\s+/g, "")}`}
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
