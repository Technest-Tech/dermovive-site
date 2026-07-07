"use client";

import { useSearchParams } from "next/navigation";
import { Search, SlidersHorizontal, X } from "lucide-react";
import { useTranslations } from "next-intl";
import { useRouter, usePathname } from "@/i18n/navigation";
import { cn } from "@/lib/utils";
import type { TagGroup } from "@/lib/types";

export type CategoryOption = { slug: string; name: string; depth: number };

const SORTS = ["newest", "price", "price_desc", "name"] as const;

/**
 * Catalog filter toolbar — search, category, skin-type/concern tags and sort.
 * Every control writes to the URL `searchParams` (via the locale-aware router)
 * so listings are shareable, SSR-able and back/forward friendly. Changing any
 * filter resets pagination to page 1.
 */
export function ProductFilters({
  categories,
  tagGroups,
}: {
  categories: CategoryOption[];
  tagGroups: TagGroup[];
}) {
  const t = useTranslations("catalog");
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();

  const category = searchParams.get("category") ?? "";
  const tag = searchParams.get("tag") ?? "";
  const sort = searchParams.get("sort") ?? "newest";
  const q = searchParams.get("q") ?? "";

  const hasFilters = Boolean(category || tag || q || (sort && sort !== "newest"));

  /** Apply param updates (null/"" clears a key), always resetting to page 1. */
  function update(changes: Record<string, string | null>) {
    const params = new URLSearchParams(searchParams.toString());
    for (const [key, value] of Object.entries(changes)) {
      if (value === null || value === "") params.delete(key);
      else params.set(key, value);
    }
    params.delete("page");
    const next = params.toString();
    router.push(next ? `${pathname}?${next}` : pathname);
  }

  function onSearch(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const value = new FormData(event.currentTarget).get("q");
    update({ q: typeof value === "string" ? value.trim() : "" });
  }

  return (
    <div className="overflow-hidden rounded-card border border-teal-700/10 bg-white/80 shadow-[var(--shadow-soft)] backdrop-blur">
      <div className="flex flex-col gap-4 border-b border-teal-700/10 bg-gradient-to-r from-white via-cream to-teal-50/70 p-4 lg:flex-row lg:items-end">
        <div className="flex min-w-0 flex-1 flex-col gap-2">
          <span className="inline-flex items-center gap-2 text-xs font-bold uppercase text-teal-700">
            <SlidersHorizontal className="h-4 w-4 text-coral-500" aria-hidden />
            {t("filters.title")}
          </span>
          <form onSubmit={onSearch} className="relative" role="search">
            <Search
              className="pointer-events-none absolute start-4 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
              aria-hidden
            />
            <input
              // Remount when the URL query changes (e.g. cleared) to stay in sync.
              key={q}
              type="search"
              name="q"
              defaultValue={q}
              placeholder={t("search.placeholder")}
              aria-label={t("search.placeholder")}
              className="h-12 w-full rounded-[1rem] border border-teal-700/15 bg-white ps-11 pe-4 text-sm text-ink shadow-[0_1px_0_rgba(255,255,255,0.85)_inset] placeholder:text-muted focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/30"
            />
          </form>
        </div>

        <div className="grid gap-3 sm:grid-cols-2 lg:w-[31rem]">
          <label className="flex flex-col gap-2 text-xs font-bold uppercase text-teal-700">
            {t("filters.category")}
            <select
              id="category"
              value={category}
              onChange={(event) => update({ category: event.target.value || null })}
              className="h-12 max-w-full rounded-[1rem] border border-teal-700/15 bg-white px-4 text-sm font-medium normal-case text-ink focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/30"
            >
              <option value="">{t("filters.allCategories")}</option>
              {categories.map((option) => (
                <option key={option.slug} value={option.slug}>
                  {`${"  ".repeat(option.depth)}${option.name}`}
                </option>
              ))}
            </select>
          </label>

          <label className="flex flex-col gap-2 text-xs font-bold uppercase text-teal-700">
            {t("sort.label")}
            <select
              id="sort"
              value={sort}
              onChange={(event) =>
                update({ sort: event.target.value === "newest" ? null : event.target.value })
              }
              className="h-12 rounded-[1rem] border border-teal-700/15 bg-white px-4 text-sm font-medium normal-case text-ink focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/30"
            >
              {SORTS.map((option) => (
                <option key={option} value={option}>
                  {t(`sort.${option}`)}
                </option>
              ))}
            </select>
          </label>
        </div>
      </div>

      <div className="space-y-5 p-4 sm:p-5">
        <div className="flex flex-wrap items-center justify-between gap-3">
          <p className="text-sm font-semibold text-teal-800">
            {t("filters.title")}
          </p>
          {hasFilters && (
            <button
              type="button"
              onClick={() =>
                update({ category: null, tag: null, q: null, sort: null })
              }
              className="inline-flex items-center gap-1.5 rounded-pill border border-coral-300/70 bg-coral-50 px-3 py-1.5 text-sm font-bold text-coral-700 transition-colors hover:border-coral-500 hover:bg-coral-100"
            >
              <X className="h-3.5 w-3.5" aria-hidden />
              {t("filters.clear")}
            </button>
          )}
        </div>

        <div className="grid gap-5 lg:grid-cols-3">
          {tagGroups.map((group) => (
            <div key={group.type} className="min-w-0">
              <p className="mb-3 text-xs font-bold uppercase text-muted">
                {t(`filters.${group.type}`)}
              </p>
              <div className="flex flex-wrap gap-2">
                {group.tags.map((option) => {
                  const active = tag === option.slug;
                  return (
                    <button
                      key={option.id}
                      type="button"
                      aria-pressed={active}
                      onClick={() => update({ tag: active ? null : option.slug })}
                      className={cn(
                        "min-h-9 rounded-pill border px-3.5 py-1.5 text-sm font-semibold transition duration-200",
                        active
                          ? "border-coral-500 bg-coral-500 text-white shadow-[var(--shadow-coral)]"
                          : "border-teal-700/15 bg-white text-teal-800 hover:border-coral-400 hover:bg-coral-50 hover:text-coral-700",
                      )}
                    >
                      {option.name}
                    </button>
                  );
                })}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
