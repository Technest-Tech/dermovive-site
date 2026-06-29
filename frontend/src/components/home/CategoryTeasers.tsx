import { Link } from "@/i18n/navigation";
import { MediaImage } from "@/components/ui/MediaImage";
import { FadeIn } from "@/components/ui/FadeIn";
import type { Category } from "@/lib/types";

/** Grid of top-level category cards linking into the catalog (Phase 4). */
export function CategoryTeasers({ categories }: { categories: Category[] }) {
  const items = categories.slice(0, 4);
  if (items.length === 0) return null;

  return (
    <div className="mt-10 grid grid-cols-2 gap-5 lg:grid-cols-4">
      {items.map((category, index) => (
        <FadeIn key={category.id} delay={index * 0.08}>
          <Link href={`/categories/${category.slug}`} className="group block">
            <div className="relative aspect-[4/5] overflow-hidden rounded-card ring-1 ring-teal-700/5">
              <MediaImage
                src={category.image?.preview ?? category.image?.original}
                alt={category.name}
                seed={category.slug}
                sizes="(max-width: 1024px) 50vw, 25vw"
                className="transition-transform duration-700 ease-[var(--ease-soft)] group-hover:scale-105"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-teal-900/75 via-teal-900/10 to-transparent" />
              <div className="absolute inset-x-0 bottom-0 p-4">
                <p className="font-display text-xl text-cream drop-shadow-sm">
                  {category.name}
                </p>
              </div>
            </div>
          </Link>
        </FadeIn>
      ))}
    </div>
  );
}
