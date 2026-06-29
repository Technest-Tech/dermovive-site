"use client";

import { useEffect, useState } from "react";
import useEmblaCarousel from "embla-carousel-react";
import { ChevronLeft, ChevronRight, ArrowRight } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { isRtl } from "@/i18n/routing";
import { ProductCard } from "@/components/product/ProductCard";
import { cn } from "@/lib/utils";
import type { ProductCard as ProductCardType } from "@/lib/types";

type ProductRailProps = {
  eyebrow: string;
  title: string;
  subtitle?: string;
  products: ProductCardType[];
  viewAllHref?: string;
};

/** Horizontally scrollable product carousel with a heading and nav arrows. */
export function ProductRail({
  eyebrow,
  title,
  subtitle,
  products,
  viewAllHref,
}: ProductRailProps) {
  const locale = useLocale();
  const tc = useTranslations("common");
  const t = useTranslations("home.slider");

  const [emblaRef, emblaApi] = useEmblaCarousel({
    align: "start",
    direction: isRtl(locale) ? "rtl" : "ltr",
    containScroll: "trimSnaps",
    dragFree: true,
  });
  const [canPrev, setCanPrev] = useState(false);
  const [canNext, setCanNext] = useState(false);

  useEffect(() => {
    if (!emblaApi) return;
    const update = () => {
      setCanPrev(emblaApi.canScrollPrev());
      setCanNext(emblaApi.canScrollNext());
    };
    update();
    emblaApi.on("select", update).on("reInit", update);
    return () => {
      emblaApi.off("select", update).off("reInit", update);
    };
  }, [emblaApi]);

  if (products.length === 0) return null;

  const showArrows = canPrev || canNext;

  return (
    <section className="container-page py-16 sm:py-20">
      <div className="flex items-end justify-between gap-6">
        <div>
          <span className="eyebrow">{eyebrow}</span>
          <h2 className="mt-3 text-3xl sm:text-4xl">{title}</h2>
          {subtitle && <p className="mt-3 max-w-md text-muted">{subtitle}</p>}
        </div>

        <div className="flex shrink-0 items-center gap-2">
          {viewAllHref && (
            <Link
              href={viewAllHref}
              className="hidden items-center gap-1 text-sm font-medium text-coral-600 hover:text-coral-700 sm:inline-flex"
            >
              {tc("viewAll")}
              <ArrowRight className="h-4 w-4 rtl:rotate-180" />
            </Link>
          )}
          {showArrows && (
            <div className="hidden items-center gap-2 sm:flex">
              <RailButton
                onClick={() => emblaApi?.scrollPrev()}
                disabled={!canPrev}
                label={t("prev")}
                icon={<ChevronLeft className="h-5 w-5 rtl:rotate-180" />}
              />
              <RailButton
                onClick={() => emblaApi?.scrollNext()}
                disabled={!canNext}
                label={t("next")}
                icon={<ChevronRight className="h-5 w-5 rtl:rotate-180" />}
              />
            </div>
          )}
        </div>
      </div>

      <div className="mt-9 overflow-hidden" ref={emblaRef}>
        <div className="flex gap-5">
          {products.map((product) => (
            <div
              key={product.id}
              className="min-w-0 flex-[0_0_64%] sm:flex-[0_0_42%] md:flex-[0_0_31%] lg:flex-[0_0_23.5%]"
            >
              <ProductCard product={product} />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function RailButton({
  onClick,
  disabled,
  label,
  icon,
}: {
  onClick: () => void;
  disabled: boolean;
  label: string;
  icon: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      aria-label={label}
      className={cn(
        "grid h-10 w-10 place-items-center rounded-full border border-teal-700/20 text-teal-800 transition-colors",
        "hover:border-teal-700 hover:bg-teal-700/5",
        "disabled:cursor-not-allowed disabled:opacity-35 disabled:hover:border-teal-700/20 disabled:hover:bg-transparent",
      )}
    >
      {icon}
    </button>
  );
}
