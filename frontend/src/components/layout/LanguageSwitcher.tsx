"use client";

import { useLocale } from "next-intl";
import { useTransition } from "react";
import { usePathname, useRouter } from "@/i18n/navigation";
import { locales, type Locale } from "@/i18n/routing";
import { cn } from "@/lib/utils";

const shortLabel: Record<Locale, string> = {
  en: "EN",
  ar: "ع",
  fr: "FR",
};

export function LanguageSwitcher() {
  const activeLocale = useLocale();
  const pathname = usePathname();
  const router = useRouter();
  const [isPending, startTransition] = useTransition();

  function switchTo(next: Locale) {
    if (next === activeLocale) return;
    startTransition(() => {
      // pathname already excludes the locale prefix; next-intl re-prefixes it.
      router.replace(pathname, { locale: next });
    });
  }

  return (
    <div
      role="group"
      aria-label="Language"
      className={cn(
        "inline-flex items-center gap-0.5 rounded-pill border border-teal-700/15 bg-white/60 p-0.5 backdrop-blur",
        isPending && "opacity-60",
      )}
    >
      {locales.map((loc) => {
        const active = loc === activeLocale;
        return (
          <button
            key={loc}
            type="button"
            onClick={() => switchTo(loc)}
            aria-current={active ? "true" : undefined}
            className={cn(
              "min-w-8 rounded-pill px-2.5 py-1 text-xs font-semibold transition-colors",
              active
                ? "bg-teal-700 text-cream"
                : "text-teal-700/70 hover:text-teal-800",
            )}
          >
            {shortLabel[loc]}
          </button>
        );
      })}
    </div>
  );
}
