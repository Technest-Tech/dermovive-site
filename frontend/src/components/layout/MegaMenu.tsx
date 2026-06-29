"use client";

import { useEffect, useRef, useState } from "react";
import { ChevronDown } from "lucide-react";
import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { cn } from "@/lib/utils";
import type { Category } from "@/lib/types";

const linkClass =
  "text-sm font-medium text-teal-800/80 transition-colors hover:text-coral-600";

/** Desktop "Shop" dropdown built from the category tree. Opens on hover/focus. */
export function MegaMenu({ categories }: { categories: Category[] }) {
  const t = useTranslations("nav");
  const [open, setOpen] = useState(false);
  const closeTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  const cancelClose = () => {
    if (closeTimer.current) clearTimeout(closeTimer.current);
  };
  const scheduleClose = () => {
    cancelClose();
    closeTimer.current = setTimeout(() => setOpen(false), 120);
  };

  useEffect(() => () => cancelClose(), []);

  useEffect(() => {
    if (!open) return;
    const onKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") setOpen(false);
    };
    document.addEventListener("keydown", onKey);
    return () => document.removeEventListener("keydown", onKey);
  }, [open]);

  // Without categories (e.g. API down) fall back to a plain link.
  if (categories.length === 0) {
    return (
      <Link href="/" className={linkClass}>
        {t("products")}
      </Link>
    );
  }

  return (
    <div
      className="relative"
      onMouseEnter={() => {
        cancelClose();
        setOpen(true);
      }}
      onMouseLeave={scheduleClose}
    >
      <button
        type="button"
        aria-expanded={open}
        aria-haspopup="true"
        onClick={() => setOpen((value) => !value)}
        onFocus={() => setOpen(true)}
        className={cn(linkClass, "inline-flex items-center gap-1")}
      >
        {t("shop")}
        <ChevronDown
          className={cn("h-4 w-4 transition-transform duration-300", open && "rotate-180")}
        />
      </button>

      {open && (
        <div
          onMouseEnter={cancelClose}
          onMouseLeave={scheduleClose}
          className="absolute left-1/2 top-full z-50 mt-3 w-[min(52rem,90vw)] -translate-x-1/2"
        >
          <div className="rounded-card border border-teal-700/10 bg-cream/95 p-6 shadow-[var(--shadow-card)] backdrop-blur">
            <div className="grid grid-cols-2 gap-x-8 gap-y-6 md:grid-cols-3">
              {categories.map((category) => (
                <div key={category.id}>
                  <Link
                    href={`/categories/${category.slug}`}
                    onClick={() => setOpen(false)}
                    className="font-display text-lg text-teal-800 transition-colors hover:text-coral-600"
                  >
                    {category.name}
                  </Link>
                  {category.children && category.children.length > 0 && (
                    <ul className="mt-2.5 space-y-2">
                      {category.children.map((child) => (
                        <li key={child.id}>
                          <Link
                            href={`/categories/${child.slug}`}
                            onClick={() => setOpen(false)}
                            className="text-sm text-muted transition-colors hover:text-coral-600"
                          >
                            {child.name}
                          </Link>
                        </li>
                      ))}
                    </ul>
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
