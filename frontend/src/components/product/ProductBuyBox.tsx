"use client";

import { useState } from "react";
import { useLocale, useTranslations } from "next-intl";
import { Heart, ShoppingBag, MessageCircle } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { buttonClasses } from "@/components/ui/Button";
import { cn } from "@/lib/utils";
import { discountPercent, formatPrice } from "@/lib/format";
import type { ProductVariant } from "@/lib/types";

/**
 * The "buy box": price (reactive to the selected variant), a variant/size
 * selector, and commerce slots. Ordering isn't live in this phase, so
 * Add-to-cart / Wishlist are disabled placeholders and the working CTA is
 * "Inquire" (→ contact). Architected so the slots light up when commerce ships.
 */
export function ProductBuyBox({
  basePrice,
  compareAt,
  currency,
  variants,
}: {
  basePrice: number | null;
  compareAt: number | null;
  currency: string;
  variants: ProductVariant[];
}) {
  const locale = useLocale();
  const t = useTranslations("product");

  const defaultIndex = variants.findIndex((variant) => variant.is_default);
  const [selected, setSelected] = useState(
    variants.length ? Math.max(defaultIndex, 0) : -1,
  );

  const variant = selected >= 0 ? variants[selected] : null;
  const price = variant?.price ?? basePrice;
  const priceLabel = formatPrice(price, currency, locale);
  const compareLabel = formatPrice(compareAt, currency, locale);
  const discount = discountPercent(price, compareAt);

  return (
    <div className="space-y-6">
      {priceLabel && (
        <div className="flex flex-wrap items-center gap-3">
          <span className="text-3xl font-semibold text-ink">{priceLabel}</span>
          {compareLabel && discount !== null && (
            <>
              <span className="text-lg text-muted line-through">{compareLabel}</span>
              <span className="rounded-pill bg-coral-500 px-2.5 py-1 text-xs font-semibold text-white shadow-[var(--shadow-coral)]">
                {t("save", { percent: discount })}
              </span>
            </>
          )}
        </div>
      )}

      {variants.length > 0 && (
        <div>
          <p className="text-sm font-medium text-teal-800">{t("variant.label")}</p>
          <div className="mt-2.5 flex flex-wrap gap-2">
            {variants.map((option, index) => (
              <button
                key={option.id}
                type="button"
                aria-pressed={index === selected}
                onClick={() => setSelected(index)}
                className={cn(
                  "rounded-pill border px-4 py-2 text-sm font-medium transition-colors",
                  index === selected
                    ? "border-teal-700 bg-teal-700 text-cream"
                    : "border-teal-700/20 text-teal-800 hover:border-teal-700",
                )}
              >
                {option.name}
              </button>
            ))}
          </div>
        </div>
      )}

      <div className="space-y-3">
        <Link
          href="/contact"
          className={buttonClasses({ size: "lg", className: "w-full" })}
        >
          <MessageCircle className="h-5 w-5" />
          {t("cta.inquire")}
        </Link>

        <div className="flex gap-3">
          <button
            type="button"
            disabled
            aria-disabled
            title={t("cta.comingSoon")}
            className={buttonClasses({
              variant: "outline",
              size: "lg",
              className: "flex-1 cursor-not-allowed",
            })}
          >
            <ShoppingBag className="h-5 w-5" />
            {t("cta.addToCart")}
            <span className="text-xs font-normal text-muted">
              ({t("cta.comingSoon")})
            </span>
          </button>

          <button
            type="button"
            disabled
            aria-disabled
            aria-label={t("cta.wishlist")}
            title={t("cta.comingSoon")}
            className={buttonClasses({
              variant: "outline",
              size: "lg",
              className: "aspect-square cursor-not-allowed px-0",
            })}
          >
            <Heart className="h-5 w-5" />
          </button>
        </div>

        <p className="text-xs leading-relaxed text-muted">{t("commerceNote")}</p>
      </div>
    </div>
  );
}
