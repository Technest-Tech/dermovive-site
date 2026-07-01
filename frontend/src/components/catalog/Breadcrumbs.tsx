import { ChevronRight } from "lucide-react";
import { Link } from "@/i18n/navigation";

export type Crumb = {
  /** Omit `href` for the current page (rendered as plain, non-linked text). */
  href?: string;
  label: string;
};

/** Accessible, RTL-aware breadcrumb trail. Separators flip with the text direction. */
export function Breadcrumbs({ items }: { items: Crumb[] }) {
  return (
    <nav aria-label="Breadcrumb" className="text-sm">
      <ol className="flex flex-wrap items-center gap-1.5 text-muted">
        {items.map((item, index) => {
          const isLast = index === items.length - 1;
          return (
            <li key={`${item.label}-${index}`} className="flex items-center gap-1.5">
              {item.href && !isLast ? (
                <Link
                  href={item.href}
                  className="transition-colors hover:text-coral-600"
                >
                  {item.label}
                </Link>
              ) : (
                <span aria-current="page" className="font-medium text-teal-800">
                  {item.label}
                </span>
              )}
              {!isLast && (
                <ChevronRight
                  className="h-3.5 w-3.5 shrink-0 text-teal-700/30 rtl:rotate-180"
                  aria-hidden
                />
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
