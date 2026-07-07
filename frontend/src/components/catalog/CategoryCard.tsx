import { Link } from "@/i18n/navigation";
import { MediaImage } from "@/components/ui/MediaImage";
import type { Category } from "@/lib/types";

const defaultSizes = "(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 18rem";

/**
 * Reusable category tile (image + name overlay) linking into the catalog.
 * Shows a localised product count when one is available.
 */
export function CategoryCard({
  category,
  sizes = defaultSizes,
  count,
}: {
  category: Category;
  sizes?: string;
  count?: string;
}) {
  return (
    <Link
      href={`/categories/${category.slug}`}
      className="group block rounded-card focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-400/60 focus-visible:ring-offset-4 focus-visible:ring-offset-cream"
    >
      <div className="relative aspect-[4/5] overflow-hidden rounded-card border border-teal-700/10 bg-white shadow-[var(--shadow-soft)] transition duration-500 ease-[var(--ease-soft)] group-hover:-translate-y-1 group-hover:border-coral-300/70 group-hover:shadow-[var(--shadow-card)]">
        <MediaImage
          src={category.image?.preview ?? category.image?.original}
          alt={category.name}
          seed={category.slug}
          sizes={sizes}
          className="transition-transform duration-700 ease-[var(--ease-soft)] group-hover:scale-[1.06]"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-teal-950/82 via-teal-900/16 to-transparent" />
        <div className="absolute inset-x-0 bottom-0 p-4">
          <span className="mb-3 inline-block h-1 w-9 rounded-full bg-coral-400 transition-all duration-300 group-hover:w-14" />
          <p className="font-display text-2xl leading-tight text-cream drop-shadow-sm">
            {category.name}
          </p>
          {count && <p className="mt-1 text-xs font-medium text-cream/78">{count}</p>}
        </div>
      </div>
    </Link>
  );
}
