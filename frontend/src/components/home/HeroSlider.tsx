"use client";

import { useCallback, useEffect, useState } from "react";
import useEmblaCarousel from "embla-carousel-react";
import { motion, useReducedMotion } from "framer-motion";
import { ChevronLeft, ChevronRight, ArrowRight } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { isRtl } from "@/i18n/routing";
import { MediaImage } from "@/components/ui/MediaImage";
import { buttonClasses } from "@/components/ui/Button";
import { resolveHeroLink } from "@/lib/format";
import { cn } from "@/lib/utils";
import type { HeroSlide } from "@/lib/types";

const AUTOPLAY_MS = 6000;

export function HeroSlider({ slides }: { slides: HeroSlide[] }) {
  if (slides.length === 0) {
    return <HeroFallback />;
  }
  return <Slider slides={slides} />;
}

function Slider({ slides }: { slides: HeroSlide[] }) {
  const locale = useLocale();
  const t = useTranslations("home");
  const tc = useTranslations("common");
  const reduceMotion = useReducedMotion();
  const multiple = slides.length > 1;

  const [emblaRef, emblaApi] = useEmblaCarousel({
    loop: true,
    align: "center",
    direction: isRtl(locale) ? "rtl" : "ltr",
  });
  const [selected, setSelected] = useState(0);
  const [paused, setPaused] = useState(false);

  useEffect(() => {
    if (!emblaApi) return;
    const onSelect = () => setSelected(emblaApi.selectedScrollSnap());
    emblaApi.on("select", onSelect);
    onSelect();
    return () => {
      emblaApi.off("select", onSelect);
    };
  }, [emblaApi]);

  useEffect(() => {
    if (!emblaApi || !multiple || paused || reduceMotion) return;
    const id = setInterval(() => emblaApi.scrollNext(), AUTOPLAY_MS);
    return () => clearInterval(id);
  }, [emblaApi, multiple, paused, reduceMotion]);

  // Pause autoplay when the tab is hidden.
  useEffect(() => {
    const onVisibility = () => setPaused(document.hidden);
    document.addEventListener("visibilitychange", onVisibility);
    return () => document.removeEventListener("visibilitychange", onVisibility);
  }, []);

  const scrollPrev = useCallback(() => emblaApi?.scrollPrev(), [emblaApi]);
  const scrollNext = useCallback(() => emblaApi?.scrollNext(), [emblaApi]);

  return (
    <section
      aria-roledescription="carousel"
      aria-label={t("hero.eyebrow")}
      className="relative"
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
      onFocusCapture={() => setPaused(true)}
      onBlurCapture={() => setPaused(false)}
    >
      <div className="overflow-hidden" ref={emblaRef}>
        <div className="flex">
          {slides.map((slide, index) => (
            <HeroSlideView
              key={slide.id}
              slide={slide}
              active={index === selected}
              priority={index === 0}
              eyebrow={t("hero.eyebrow")}
              fallbackCta={tc("explore")}
              reduceMotion={!!reduceMotion}
            />
          ))}
        </div>
      </div>

      {multiple && (
        <>
          <button
            type="button"
            onClick={scrollPrev}
            aria-label={t("slider.prev")}
            className="absolute start-3 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-white/15 text-white ring-1 ring-white/25 backdrop-blur transition-colors hover:bg-white/25 sm:start-6"
          >
            <ChevronLeft className="h-5 w-5 rtl:rotate-180" />
          </button>
          <button
            type="button"
            onClick={scrollNext}
            aria-label={t("slider.next")}
            className="absolute end-3 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-white/15 text-white ring-1 ring-white/25 backdrop-blur transition-colors hover:bg-white/25 sm:end-6"
          >
            <ChevronRight className="h-5 w-5 rtl:rotate-180" />
          </button>

          <div className="absolute bottom-5 start-1/2 z-10 flex -translate-x-1/2 items-center gap-2 rtl:translate-x-1/2">
            {slides.map((slide, index) => (
              <button
                key={slide.id}
                type="button"
                onClick={() => emblaApi?.scrollTo(index)}
                aria-label={t("slider.goTo", { n: index + 1 })}
                aria-current={index === selected ? "true" : undefined}
                className={cn(
                  "h-2 rounded-pill transition-all duration-300",
                  index === selected
                    ? "w-7 bg-coral-400"
                    : "w-2 bg-white/50 hover:bg-white/80",
                )}
              />
            ))}
          </div>
        </>
      )}
    </section>
  );
}

function HeroSlideView({
  slide,
  active,
  priority,
  eyebrow,
  fallbackCta,
  reduceMotion,
}: {
  slide: HeroSlide;
  active: boolean;
  priority: boolean;
  eyebrow: string;
  fallbackCta: string;
  reduceMotion: boolean;
}) {
  const link = resolveHeroLink(slide.link);
  const ctaLabel = slide.cta_label ?? fallbackCta;
  const shown = active ? { opacity: 1, y: 0 } : { opacity: 0, y: 24 };

  return (
    <div className="relative min-w-0 flex-[0_0_100%]">
      <div className="relative aspect-[16/11] w-full overflow-hidden sm:aspect-[2/1] lg:aspect-[21/8]">
        {slide.image ? (
          <>
            <MediaImage
              src={slide.image.preview ?? slide.image.original}
              alt={slide.title}
              priority={priority}
              sizes="100vw"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-teal-950/80 via-teal-900/35 to-teal-900/25" />
          </>
        ) : (
          <div className="absolute inset-0 bg-gradient-to-br from-teal-700 via-teal-800 to-teal-950">
            <div className="pointer-events-none absolute -top-24 end-10 h-80 w-80 rounded-full bg-coral-400/20 blur-3xl" />
            <div className="pointer-events-none absolute -bottom-32 start-0 h-96 w-96 rounded-full bg-teal-300/10 blur-3xl" />
          </div>
        )}

        <div className="absolute inset-0 flex items-center">
          <div className="container-page">
            <motion.div
              className="max-w-xl"
              initial={false}
              animate={reduceMotion ? { opacity: 1, y: 0 } : shown}
              transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
            >
              <span className="eyebrow text-coral-300">{eyebrow}</span>
              <h1 className="mt-4 text-4xl leading-[1.07] text-cream sm:text-5xl lg:text-6xl">
                {slide.title}
              </h1>
              {slide.subtitle && (
                <p className="mt-5 max-w-md text-base leading-relaxed text-teal-50/85 sm:text-lg">
                  {slide.subtitle}
                </p>
              )}
              <div className="mt-8">
                {link.external ? (
                  <a href={link.href} className={buttonClasses({ size: "lg" })}>
                    {ctaLabel}
                    <ArrowRight className="h-4 w-4 rtl:rotate-180" />
                  </a>
                ) : (
                  <Link href={link.href} className={buttonClasses({ size: "lg" })}>
                    {ctaLabel}
                    <ArrowRight className="h-4 w-4 rtl:rotate-180" />
                  </Link>
                )}
              </div>
            </motion.div>
          </div>
        </div>
      </div>
    </div>
  );
}

/** Shown when the API returns no slides (e.g. backend unreachable). */
function HeroFallback() {
  const t = useTranslations("home.hero");
  const tc = useTranslations("common");

  return (
    <section className="relative overflow-hidden">
      <div className="relative aspect-[16/11] w-full overflow-hidden bg-gradient-to-br from-teal-700 via-teal-800 to-teal-950 sm:aspect-[2/1] lg:aspect-[21/8]">
        <div className="pointer-events-none absolute -top-24 end-10 h-80 w-80 rounded-full bg-coral-400/20 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-32 start-0 h-96 w-96 rounded-full bg-teal-300/10 blur-3xl" />
        <div className="absolute inset-0 flex items-center">
          <div className="container-page">
            <div className="max-w-xl">
              <span className="eyebrow text-coral-300">{t("eyebrow")}</span>
              <h1 className="mt-4 text-4xl leading-[1.07] text-cream sm:text-5xl lg:text-6xl">
                {t("title")}
              </h1>
              <p className="mt-5 max-w-md text-base leading-relaxed text-teal-50/85 sm:text-lg">
                {t("subtitle")}
              </p>
              <div className="mt-8">
                <Link href="/" className={buttonClasses({ size: "lg" })}>
                  {tc("explore")}
                  <ArrowRight className="h-4 w-4 rtl:rotate-180" />
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
