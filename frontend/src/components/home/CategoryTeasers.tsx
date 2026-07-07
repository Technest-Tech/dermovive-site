import { Link } from "@/i18n/navigation";
import { MediaImage } from "@/components/ui/MediaImage";
import { FadeIn } from "@/components/ui/FadeIn";
import type { Category } from "@/lib/types";

/** Grid of top-level category cards linking into the catalog (Phase 4). */
export function CategoryTeasers({ categories }: { categories: Category[] }) {
  const items = categories.slice(0, 4);
  if (items.length === 0) return null;

  return (
    <div className="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      {items.map((category, index) => (
        <FadeIn key={category.id} delay={index * 0.08}>
          <Link
            href={`/categories/${category.slug}`}
            className="group block rounded-card focus:outline-none focus-visible:ring-2 focus-visible:ring-coral-400/60 focus-visible:ring-offset-4 focus-visible:ring-offset-cream"
          >
            <div className="relative aspect-[4/5] overflow-hidden rounded-card border border-teal-700/10 bg-white shadow-[var(--shadow-soft)] transition duration-500 ease-[var(--ease-soft)] group-hover:-translate-y-1 group-hover:border-coral-300/70 group-hover:shadow-[var(--shadow-card)] sm:aspect-[5/6]">
              <MediaImage
                src={category.image?.preview ?? category.image?.original}
                alt={category.name}
                seed={category.slug}
                sizes="(max-width: 1024px) 50vw, 25vw"
                className="transition-transform duration-700 ease-[var(--ease-soft)] group-hover:scale-[1.06]"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-teal-950/82 via-teal-900/16 to-transparent" />
              <div className="absolute inset-x-0 bottom-0 p-4 sm:p-5">
                <span className="mb-3 inline-block h-1 w-9 rounded-full bg-coral-400 transition-all duration-300 group-hover:w-14" />
                <p className="font-display text-2xl leading-tight text-cream drop-shadow-sm">
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
