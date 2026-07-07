import { useLocale, useTranslations } from "next-intl";
import { ArrowUpRight } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { MediaImage } from "@/components/ui/MediaImage";
import { discountPercent, formatPrice } from "@/lib/format";
import type { ProductCard as ProductCardType } from "@/lib/types";

const cardSizes =
  "(max-width: 640px) 60vw, (max-width: 1024px) 33vw, 22rem";

/** Product card shared by rails and catalog grids. */
export function ProductCard({ product }: { product: ProductCardType }) {
  const locale = useLocale();
  const t = useTranslations("product");

  const price = formatPrice(product.price, product.currency, locale);
  const compareAt = formatPrice(product.compare_at_price, product.currency, locale);
  const discount = discountPercent(product.price, product.compare_at_price);

  return (
    <Link
      href={`/products/${product.slug}`}
      className="group block h-full rounded-card focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-400/60 focus-visible:ring-offset-4 focus-visible:ring-offset-cream"
    >
      <div className="flex h-full flex-col overflow-hidden rounded-card border border-teal-700/10 bg-white/80 shadow-[0_1px_0_rgba(255,255,255,0.75)_inset,0_18px_45px_rgba(28,74,69,0.08)] backdrop-blur transition duration-500 ease-[var(--ease-soft)] group-hover:-translate-y-1 group-hover:border-coral-300/70 group-hover:shadow-[0_22px_60px_rgba(28,74,69,0.13)]">
        <div className="relative m-2 aspect-[4/5] overflow-hidden rounded-[1rem] bg-gradient-to-br from-sand via-cream to-teal-50">
          <MediaImage
            src={product.image?.thumb ?? product.image?.original}
            alt={product.name}
            seed={product.slug}
            sizes={cardSizes}
            className="transition-transform duration-700 ease-[var(--ease-soft)] group-hover:scale-[1.06]"
          />

          <div className="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-teal-950/30 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100" />

          <div className="absolute start-3 top-3 flex max-w-[calc(100%-1.5rem)] flex-wrap items-start gap-1.5">
            {product.badges.slice(0, 2).map((badge) => (
              <span
                key={badge.value}
                className="rounded-pill bg-white/92 px-2.5 py-1 text-[0.62rem] font-bold uppercase text-teal-800 shadow-[var(--shadow-soft)] ring-1 ring-white/75 backdrop-blur"
              >
                {t(`badge.${badge.value}`)}
              </span>
            ))}
            {discount !== null && (
              <span className="rounded-pill bg-coral-500 px-2.5 py-1 text-[0.62rem] font-bold text-white shadow-[var(--shadow-coral)]">
                {t("save", { percent: discount })}
              </span>
            )}
          </div>

          <span className="absolute end-3 top-3 grid h-9 w-9 translate-y-1 place-items-center rounded-full bg-white/90 text-teal-800 opacity-0 shadow-[var(--shadow-soft)] ring-1 ring-teal-700/10 backdrop-blur transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
            <ArrowUpRight className="h-4 w-4 rtl:-rotate-90" aria-hidden />
          </span>
        </div>

        <div className="flex flex-1 flex-col px-4 pb-4 pt-2">
          {product.primary_category?.name && (
            <p className="text-[0.68rem] font-bold uppercase text-coral-600">
              {product.primary_category.name}
            </p>
          )}
          <h3 className="mt-1 line-clamp-2 min-h-[3.1rem] font-display text-xl leading-tight text-teal-800 transition-colors group-hover:text-coral-700">
            {product.name}
          </h3>
          {price && (
            <div className="mt-auto flex items-end justify-between gap-3 border-t border-teal-700/10 pt-3">
              <div className="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                <span className="text-[0.95rem] font-extrabold text-ink">{price}</span>
                {compareAt && (
                  <span className="text-xs font-medium text-muted line-through">
                    {compareAt}
                  </span>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </Link>
  );
}
