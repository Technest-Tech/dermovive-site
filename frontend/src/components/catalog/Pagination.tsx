import { ChevronLeft, ChevronRight } from "lucide-react";
import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import { cn } from "@/lib/utils";
import type { PaginationMeta, ProductQuery } from "@/lib/types";

/** Build a windowed page sequence with `null` gaps for ellipsis. */
function pageWindow(current: number, last: number): (number | null)[] {
  if (last <= 7) {
    return Array.from({ length: last }, (_, index) => index + 1);
  }
  const pages = new Set([1, last, current, current - 1, current + 1]);
  const sorted = [...pages].filter((page) => page >= 1 && page <= last).sort((a, b) => a - b);

  const result: (number | null)[] = [];
  let previous = 0;
  for (const page of sorted) {
    if (page - previous > 1) result.push(null);
    result.push(page);
    previous = page;
  }
  return result;
}

/**
 * SSR-friendly pagination. Each page is a real, shareable link that preserves
 * the active filters/sort/search. RTL is handled by the flex flow + icon flip.
 */
export async function Pagination({
  meta,
  query,
  locale,
  basePath = "/products",
}: {
  meta: PaginationMeta;
  query: ProductQuery;
  locale: string;
  basePath?: string;
}) {
  if (meta.last_page <= 1) return null;

  const t = await getTranslations({ locale, namespace: "catalog.pagination" });

  const hrefFor = (page: number) => {
    const params = new URLSearchParams();
    for (const [key, value] of Object.entries(query)) {
      if (key !== "page" && value) params.set(key, value);
    }
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `${basePath}?${qs}` : basePath;
  };

  const current = meta.current_page;
  const pages = pageWindow(current, meta.last_page);

  const edgeClass =
    "inline-flex h-10 items-center gap-1 rounded-pill border border-teal-700/15 px-4 text-sm font-medium text-teal-800 transition-colors hover:border-teal-700 hover:bg-teal-700/5";

  return (
    <nav aria-label={t("label")} className="flex items-center justify-center gap-2">
      {current > 1 ? (
        <Link href={hrefFor(current - 1)} className={edgeClass} rel="prev">
          <ChevronLeft className="h-4 w-4 rtl:rotate-180" aria-hidden />
          <span className="hidden sm:inline">{t("prev")}</span>
        </Link>
      ) : (
        <span className={cn(edgeClass, "cursor-not-allowed opacity-40")} aria-disabled>
          <ChevronLeft className="h-4 w-4 rtl:rotate-180" aria-hidden />
          <span className="hidden sm:inline">{t("prev")}</span>
        </span>
      )}

      <ul className="flex items-center gap-1.5">
        {pages.map((page, index) =>
          page === null ? (
            <li key={`gap-${index}`} className="px-1 text-muted" aria-hidden>
              …
            </li>
          ) : (
            <li key={page}>
              <Link
                href={hrefFor(page)}
                aria-current={page === current ? "page" : undefined}
                className={cn(
                  "grid h-10 w-10 place-items-center rounded-full text-sm font-medium transition-colors",
                  page === current
                    ? "bg-teal-700 text-cream"
                    : "text-teal-800 hover:bg-teal-700/5",
                )}
              >
                {page}
              </Link>
            </li>
          ),
        )}
      </ul>

      {current < meta.last_page ? (
        <Link href={hrefFor(current + 1)} className={edgeClass} rel="next">
          <span className="hidden sm:inline">{t("next")}</span>
          <ChevronRight className="h-4 w-4 rtl:rotate-180" aria-hidden />
        </Link>
      ) : (
        <span className={cn(edgeClass, "cursor-not-allowed opacity-40")} aria-disabled>
          <span className="hidden sm:inline">{t("next")}</span>
          <ChevronRight className="h-4 w-4 rtl:rotate-180" aria-hidden />
        </span>
      )}
    </nav>
  );
}
