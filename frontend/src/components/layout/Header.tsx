import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { Logo } from "./Logo";
import { LanguageSwitcher } from "./LanguageSwitcher";

const navItems = [
  { key: "products", href: "/#collection" },
  { key: "categories", href: "/#categories" },
  { key: "about", href: "/#story" },
  { key: "contact", href: "/#contact" },
] as const;

export function Header() {
  const t = useTranslations("nav");
  const tc = useTranslations("common");

  return (
    <header className="sticky top-0 z-50 border-b border-teal-700/10 bg-cream/80 backdrop-blur-md">
      <div className="container-page flex h-18 items-center justify-between gap-6 py-3">
        <Logo />

        <nav className="hidden items-center gap-8 lg:flex">
          {navItems.map((item) => (
            <Link
              key={item.key}
              href={item.href}
              className="text-sm font-medium text-teal-800/80 transition-colors hover:text-coral-600"
            >
              {t(item.key)}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-3">
          <LanguageSwitcher />
          <Link
            href="/#collection"
            className={buttonClasses({ size: "sm", className: "hidden sm:inline-flex" })}
          >
            {tc("explore")}
          </Link>
        </div>
      </div>
    </header>
  );
}
