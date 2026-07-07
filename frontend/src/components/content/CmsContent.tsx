import { cn } from "@/lib/utils";

/**
 * Renders trusted, admin-authored HTML from the CMS (Filament RichEditor) with
 * on-brand typography. Bodies come from our own backend, so `dangerouslySet…`
 * is acceptable here; do not use this for untrusted input.
 */
export function CmsContent({
  html,
  className,
}: {
  html: string;
  className?: string;
}) {
  return (
    <div
      className={cn(
        "max-w-none text-lg leading-relaxed text-muted",
        "[&_p]:mb-4",
        "[&_h2]:mb-3 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:text-teal-800",
        "[&_h3]:mb-2 [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:text-teal-800",
        "[&_ul]:my-4 [&_ul]:list-disc [&_ul]:ps-6 [&_ol]:my-4 [&_ol]:list-decimal [&_ol]:ps-6 [&_li]:mb-1.5",
        "[&_figure]:my-8 [&_figure]:overflow-hidden [&_figure]:rounded-card [&_figure]:bg-sand",
        "[&_img]:aspect-[16/9] [&_img]:h-auto [&_img]:w-full [&_img]:object-cover",
        "[&_a]:text-coral-600 [&_a]:underline hover:[&_a]:text-coral-700",
        "[&_strong]:font-semibold [&_strong]:text-teal-800",
        className,
      )}
      dangerouslySetInnerHTML={{ __html: html }}
    />
  );
}
