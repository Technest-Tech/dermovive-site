import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";

/**
 * Localized 404 for `notFound()` thrown anywhere under `[locale]` (unknown
 * product/category slugs, etc.). Renders inside the locale layout, so it keeps
 * the site chrome and translations. Sync server component → `useTranslations`.
 */
export default function NotFound() {
  const t = useTranslations("notFound");

  return (
    <div className="container-page grid min-h-[60vh] place-items-center py-20 text-center">
      <div>
        <p className="font-display text-7xl text-coral-500 sm:text-8xl">404</p>
        <h1 className="mt-4 text-3xl sm:text-4xl">{t("title")}</h1>
        <p className="mx-auto mt-3 max-w-md text-muted">{t("body")}</p>
        <div className="mt-8 flex flex-wrap justify-center gap-3">
          <Link href="/" className={buttonClasses()}>
            {t("home")}
          </Link>
          <Link href="/products" className={buttonClasses({ variant: "outline" })}>
            {t("browse")}
          </Link>
        </div>
      </div>
    </div>
  );
}
