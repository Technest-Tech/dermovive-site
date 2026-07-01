"use client";

import { useSearchParams } from "next/navigation";
import { Search, X } from "lucide-react";
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
    <div className="space-y-5">
      {/* Search + sort */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
        <form onSubmit={onSearch} className="relative flex-1" role="search">
          <Search
            className="pointer-events-none absolute start-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
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
            className="h-11 w-full rounded-pill border border-teal-700/15 bg-white ps-10 pe-4 text-sm text-ink placeholder:text-muted focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/30"
          />
        </form>

        <div className="flex items-center gap-2">
          <label htmlFor="sort" className="shrink-0 text-sm text-muted">
            {t("sort.label")}
          </label>
          <select
            id="sort"
            value={sort}
            onChange={(event) =>
              update({ sort: event.target.value === "newest" ? null : event.target.value })
            }
            className="h-11 rounded-pill border border-teal-700/15 bg-white px-4 text-sm text-ink focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/30"
          >
            {SORTS.map((option) => (
              <option key={option} value={option}>
                {t(`sort.${option}`)}
              </option>
            ))}
          </select>
        </div>
      </div>

      {/* Category + tag chips */}
      <div className="space-y-4 rounded-card border border-teal-700/10 bg-white/60 p-5">
        <div className="flex flex-wrap items-center gap-3">
          <label htmlFor="category" className="text-sm font-medium text-teal-800">
            {t("filters.category")}
          </label>
          <select
            id="category"
            value={category}
            onChange={(event) => update({ category: event.target.value || null })}
            className="h-10 max-w-full rounded-pill border border-teal-700/15 bg-white px-4 text-sm text-ink focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/30"
          >
            <option value="">{t("filters.allCategories")}</option>
            {categories.map((option) => (
              <option key={option.slug} value={option.slug}>
                {`${"  ".repeat(option.depth)}${option.name}`}
              </option>
            ))}
          </select>

          {hasFilters && (
            <button
              type="button"
              onClick={() =>
                update({ category: null, tag: null, q: null, sort: null })
              }
              className="inline-flex items-center gap-1 text-sm font-medium text-coral-600 hover:text-coral-700 ms-auto"
            >
              <X className="h-3.5 w-3.5" aria-hidden />
              {t("filters.clear")}
            </button>
          )}
        </div>

        {tagGroups.map((group) => (
          <div key={group.type}>
            <p className="mb-2 text-xs font-semibold uppercase tracking-wider text-muted">
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
                      "rounded-pill border px-3.5 py-1.5 text-sm transition-colors",
                      active
                        ? "border-coral-500 bg-coral-500 text-white shadow-[var(--shadow-coral)]"
                        : "border-teal-700/15 bg-white text-teal-800 hover:border-coral-400 hover:text-coral-700",
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
  );
}
