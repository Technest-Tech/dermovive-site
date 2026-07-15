"use client";

import { useEffect, useRef, useState } from "react";
import { Check, ChevronDown, LayoutGrid } from "lucide-react";
import { cn } from "@/lib/utils";
import type { CategoryOption } from "./ProductFilters";

/**
 * Accessible, custom-styled category picker used in the catalog toolbar.
 * Replaces the native <select> with a modern popover: rounded panel, hover
 * states, a check on the active option and depth-indented sub-categories.
 * Emits "" for the "all categories" choice so the parent can clear the param.
 */
export function CategoryDropdown({
  options,
  value,
  onChange,
  allLabel,
  label,
}: {
  options: CategoryOption[];
  value: string;
  onChange: (slug: string) => void;
  allLabel: string;
  label: string;
}) {
  const [open, setOpen] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  const selected = options.find((option) => option.slug === value);
  const current = selected?.name ?? allLabel;

  useEffect(() => {
    if (!open) return;

    function onPointerDown(event: MouseEvent) {
      if (ref.current && !ref.current.contains(event.target as Node)) {
        setOpen(false);
      }
    }
    function onKeyDown(event: KeyboardEvent) {
      if (event.key === "Escape") setOpen(false);
    }

    document.addEventListener("mousedown", onPointerDown);
    document.addEventListener("keydown", onKeyDown);
    return () => {
      document.removeEventListener("mousedown", onPointerDown);
      document.removeEventListener("keydown", onKeyDown);
    };
  }, [open]);

  function choose(slug: string) {
    onChange(slug);
    setOpen(false);
  }

  const items: CategoryOption[] = [
    { slug: "", name: allLabel, depth: 0 },
    ...options,
  ];

  return (
    <div ref={ref} className="relative flex flex-col gap-2">
      <span className="text-xs font-bold uppercase text-teal-700">{label}</span>

      <button
        type="button"
        onClick={() => setOpen((prev) => !prev)}
        aria-haspopup="listbox"
        aria-expanded={open}
        className={cn(
          "flex h-12 w-full items-center justify-between gap-2 rounded-[1rem] border bg-white px-4 text-sm font-medium text-ink transition-colors",
          open
            ? "border-coral-400 ring-2 ring-coral-400/30"
            : "border-teal-700/15 hover:border-coral-300",
        )}
      >
        <span className="inline-flex min-w-0 items-center gap-2">
          <LayoutGrid className="h-4 w-4 shrink-0 text-coral-500" aria-hidden />
          <span className="truncate">{current}</span>
        </span>
        <ChevronDown
          className={cn(
            "h-4 w-4 shrink-0 text-teal-700/60 transition-transform duration-200",
            open && "rotate-180",
          )}
          aria-hidden
        />
      </button>

      {open && (
        <div
          role="listbox"
          style={{ animation: "catdd-in 150ms ease-out" }}
          className="absolute top-full z-30 mt-2 max-h-72 w-full origin-top overflow-auto rounded-[1rem] border border-teal-700/10 bg-white p-1.5 shadow-[0_16px_40px_-12px_rgba(15,73,74,0.35)]"
        >
          {items.map((option) => {
            const active = (option.slug || "") === (value || "");
            return (
              <button
                key={option.slug || "__all"}
                type="button"
                role="option"
                aria-selected={active}
                onClick={() => choose(option.slug)}
                style={{ paddingInlineStart: `${0.75 + option.depth * 0.9}rem` }}
                className={cn(
                  "flex w-full items-center justify-between gap-2 rounded-[0.7rem] py-2.5 pe-3 text-start text-sm transition-colors",
                  active
                    ? "bg-coral-50 font-semibold text-coral-700"
                    : "text-teal-800 hover:bg-teal-50",
                )}
              >
                <span className="truncate">{option.name}</span>
                {active && (
                  <Check className="h-4 w-4 shrink-0 text-coral-500" aria-hidden />
                )}
              </button>
            );
          })}
        </div>
      )}
    </div>
  );
}
