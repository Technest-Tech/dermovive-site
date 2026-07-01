"use client";

import { useEffect, useState } from "react";
import { Menu, X } from "lucide-react";
import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import type { Category } from "@/lib/types";

const staticLinks = [
  { key: "products", href: "/products" },
  { key: "about", href: "/our-story" },
  { key: "contact", href: "/contact" },
] as const;

/** Hamburger + slide-down drawer for small screens, fed by the category tree. */
export function MobileMenu({ categories }: { categories: Category[] }) {
  const t = useTranslations("nav");
  const [open, setOpen] = useState(false);
  const close = () => setOpen(false);

  useEffect(() => {
    if (!open) return;
    const onKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") setOpen(false);
    };
    document.addEventListener("keydown", onKey);
    document.body.style.overflow = "hidden";
    return () => {
      document.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
    };
  }, [open]);

  return (
    <div className="lg:hidden">
      <button
        type="button"
        aria-label={t("menu")}
        aria-expanded={open}
        onClick={() => setOpen((value) => !value)}
        className="grid h-10 w-10 place-items-center rounded-full text-teal-800 transition-colors hover:bg-teal-700/5"
      >
        {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
      </button>

      {open && (
        <>
          <div
            className="fixed inset-x-0 bottom-0 top-18 z-40 bg-ink/20 backdrop-blur-sm"
            onClick={close}
          />
          <nav
            aria-label={t("menu")}
            className="fixed inset-x-0 top-18 z-40 max-h-[80vh] overflow-y-auto border-b border-teal-700/10 bg-cream shadow-[var(--shadow-card)]"
          >
            <div className="container-page space-y-6 py-6">
              {categories.length > 0 && (
                <div>
                  <p className="eyebrow">{t("shop")}</p>
                  <ul className="mt-3 space-y-4">
                    {categories.map((category) => (
                      <li key={category.id}>
                        <Link
                          href={`/categories/${category.slug}`}
                          onClick={close}
                          className="font-display text-lg text-teal-800"
                        >
                          {category.name}
                        </Link>
                        {category.children && category.children.length > 0 && (
                          <ul className="mt-2 flex flex-wrap gap-x-4 gap-y-1.5 ps-1">
                            {category.children.map((child) => (
                              <li key={child.id}>
                                <Link
                                  href={`/categories/${child.slug}`}
                                  onClick={close}
                                  className="text-sm text-muted hover:text-coral-600"
                                >
                                  {child.name}
                                </Link>
                              </li>
                            ))}
                          </ul>
                        )}
                      </li>
                    ))}
                  </ul>
                </div>
              )}

              <ul className="space-y-3 border-t border-teal-700/10 pt-5">
                {staticLinks.map((item) => (
                  <li key={item.key}>
                    <Link
                      href={item.href}
                      onClick={close}
                      className="text-sm font-medium text-teal-800/80 hover:text-coral-600"
                    >
                      {t(item.key)}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          </nav>
        </>
      )}
    </div>
  );
}
