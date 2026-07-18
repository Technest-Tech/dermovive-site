import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { Logo } from "./Logo";
import { LanguageSwitcher } from "./LanguageSwitcher";
import { MegaMenu } from "./MegaMenu";
import { MobileMenu } from "./MobileMenu";
import type { Category } from "@/lib/types";

const navLinkClass =
  "text-sm font-medium text-teal-800/80 transition-colors hover:text-coral-600";

export function Header({ categories }: { categories: Category[] }) {
  const t = useTranslations("nav");
  const tc = useTranslations("common");

  return (
    <header className="sticky top-0 z-50 border-b border-teal-700/10 bg-cream/80 backdrop-blur-md">
      <div className="container-page flex h-18 items-center justify-between gap-6 py-3">
        <Logo />

        <nav className="hidden items-center gap-8 lg:flex">
          <Link href="/" className={navLinkClass}>
            {t("home")}
          </Link>
          <Link href="/products" className={navLinkClass}>
            {t("products")}
          </Link>
          <MegaMenu categories={categories} />
          <Link href="/contact" className={navLinkClass}>
            {t("contact")}
          </Link>
        </nav>

        <div className="flex items-center gap-2 sm:gap-3">
          <LanguageSwitcher />
          <Link
            href="/#collection"
            className={buttonClasses({ size: "sm", className: "hidden sm:inline-flex" })}
          >
            {tc("explore")}
          </Link>
          <MobileMenu categories={categories} />
        </div>
      </div>
    </header>
  );
}
