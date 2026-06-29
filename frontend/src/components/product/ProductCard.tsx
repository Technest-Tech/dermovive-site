import { useLocale, useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { MediaImage } from "@/components/ui/MediaImage";
import { discountPercent, formatPrice } from "@/lib/format";
import type { ProductCard as ProductCardType } from "@/lib/types";

const cardSizes =
  "(max-width: 640px) 60vw, (max-width: 1024px) 33vw, 22rem";

/** Product "card" for rails and (later) catalog grids. Links to the detail page. */
export function ProductCard({ product }: { product: ProductCardType }) {
  const locale = useLocale();
  const t = useTranslations("product");

  const price = formatPrice(product.price, product.currency, locale);
  const compareAt = formatPrice(product.compare_at_price, product.currency, locale);
  const discount = discountPercent(product.price, product.compare_at_price);

  return (
    <Link href={`/products/${product.slug}`} className="group block">
      <div className="relative aspect-[4/5] overflow-hidden rounded-card bg-sand ring-1 ring-teal-700/5">
        <MediaImage
          src={product.image?.thumb ?? product.image?.original}
          alt={product.name}
          seed={product.slug}
          sizes={cardSizes}
          className="transition-transform duration-700 ease-[var(--ease-soft)] group-hover:scale-105"
        />

        <div className="absolute start-3 top-3 flex flex-col items-start gap-1.5">
          {product.badges.map((badge) => (
            <span
              key={badge.value}
              className="rounded-pill bg-white/90 px-2.5 py-1 text-[0.62rem] font-semibold uppercase tracking-wide text-teal-800 shadow-[var(--shadow-soft)] backdrop-blur"
            >
              {t(`badge.${badge.value}`)}
            </span>
          ))}
          {discount !== null && (
            <span className="rounded-pill bg-coral-500 px-2.5 py-1 text-[0.62rem] font-semibold text-white shadow-[var(--shadow-coral)]">
              {t("save", { percent: discount })}
            </span>
          )}
        </div>
      </div>

      <div className="mt-3.5">
        {product.primary_category?.name && (
          <p className="text-[0.7rem] font-medium uppercase tracking-wider text-coral-600">
            {product.primary_category.name}
          </p>
        )}
        <h3 className="mt-1 font-display text-lg leading-snug text-teal-800 transition-colors group-hover:text-coral-700">
          {product.name}
        </h3>
        {price && (
          <div className="mt-1.5 flex items-center gap-2">
            <span className="text-sm font-semibold text-ink">{price}</span>
            {compareAt && (
              <span className="text-xs text-muted line-through">{compareAt}</span>
            )}
          </div>
        )}
      </div>
    </Link>
  );
}
