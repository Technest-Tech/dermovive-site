"use client";

import { useState } from "react";
import { useTranslations } from "next-intl";
import { Check } from "lucide-react";
import { cn } from "@/lib/utils";

type TabKey = "benefits" | "ingredients" | "howToUse";

/**
 * Tabbed rich content: benefits (checklist), ingredients (prose) and how-to-use
 * (numbered steps). Only tabs with content are shown; nothing renders if the
 * product has none.
 */
export function ProductTabs({
  benefits,
  ingredients,
  howToUse,
}: {
  benefits: string[];
  ingredients: string | null;
  howToUse: string[];
}) {
  const t = useTranslations("product.tabs");

  const tabs = (
    [
      benefits.length ? "benefits" : null,
      ingredients ? "ingredients" : null,
      howToUse.length ? "howToUse" : null,
    ].filter(Boolean) as TabKey[]
  );

  const [active, setActive] = useState<TabKey>(tabs[0]);

  if (tabs.length === 0) return null;

  return (
    <div>
      <div
        role="tablist"
        aria-label={t("label")}
        className="flex flex-wrap gap-2 border-b border-teal-700/10"
      >
        {tabs.map((tab) => (
          <button
            key={tab}
            type="button"
            role="tab"
            id={`tab-${tab}`}
            aria-selected={active === tab}
            aria-controls={`panel-${tab}`}
            onClick={() => setActive(tab)}
            className={cn(
              "-mb-px border-b-2 px-4 py-3 text-sm font-medium transition-colors",
              active === tab
                ? "border-coral-500 text-coral-700"
                : "border-transparent text-teal-800/70 hover:text-teal-800",
            )}
          >
            {t(tab)}
          </button>
        ))}
      </div>

      <div
        role="tabpanel"
        id={`panel-${active}`}
        aria-labelledby={`tab-${active}`}
        className="py-7"
      >
        {active === "benefits" && (
          <ul className="grid gap-3 sm:grid-cols-2">
            {benefits.map((benefit, index) => (
              <li key={index} className="flex items-start gap-3">
                <span className="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-coral-100 text-coral-600">
                  <Check className="h-3.5 w-3.5" />
                </span>
                <span className="text-teal-800">{benefit}</span>
              </li>
            ))}
          </ul>
        )}

        {active === "ingredients" && ingredients && (
          <p className="max-w-2xl leading-relaxed text-muted">{ingredients}</p>
        )}

        {active === "howToUse" && (
          <ol className="space-y-4">
            {howToUse.map((step, index) => (
              <li key={index} className="flex items-start gap-4">
                <span className="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-teal-700 text-sm font-semibold text-cream">
                  {index + 1}
                </span>
                <span className="pt-1 text-teal-800">{step}</span>
              </li>
            ))}
          </ol>
        )}
      </div>
    </div>
  );
}
