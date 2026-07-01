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
    <Link href={`/categories/${category.slug}`} className="group block">
      <div className="relative aspect-[4/5] overflow-hidden rounded-card ring-1 ring-teal-700/5">
        <MediaImage
          src={category.image?.preview ?? category.image?.original}
          alt={category.name}
          seed={category.slug}
          sizes={sizes}
          className="transition-transform duration-700 ease-[var(--ease-soft)] group-hover:scale-105"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-teal-900/75 via-teal-900/10 to-transparent" />
        <div className="absolute inset-x-0 bottom-0 p-4">
          <p className="font-display text-xl text-cream drop-shadow-sm">
            {category.name}
          </p>
          {count && <p className="mt-0.5 text-xs text-cream/75">{count}</p>}
        </div>
      </div>
    </Link>
  );
}
