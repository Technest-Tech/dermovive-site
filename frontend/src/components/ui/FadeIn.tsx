"use client";

import { motion } from "framer-motion";
import type { ReactNode } from "react";

type FadeInProps = {
  children: ReactNode;
  delay?: number;
  className?: string;
  /** When true, animates on entering the viewport instead of on mount. */
  onScroll?: boolean;
};

const transition = { duration: 0.7, ease: [0.22, 1, 0.36, 1] as const };
const hidden = { opacity: 0, y: 24 };
const shown = { opacity: 1, y: 0 };

export function FadeIn({ children, delay = 0, className, onScroll = true }: FadeInProps) {
  return (
    <motion.div
      className={className}
      initial={hidden}
      {...(onScroll
        ? { whileInView: shown, viewport: { once: true, margin: "-80px" } }
        : { animate: shown })}
      transition={{ ...transition, delay }}
    >
      {children}
    </motion.div>
  );
}
