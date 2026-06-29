/**
 * Typed, locale-aware fetchers for the public API. Each one degrades
 * gracefully: on a network/HTTP error it logs and returns a safe empty value
 * so pages still render (with fallbacks) and `next build` never depends on the
 * backend being reachable. Time-based revalidation/ISR is deferred to Phase 6.
 */
import { apiFetch } from "./api";
import type { Category, HomePayload, Settings } from "./types";

type Envelope<T> = { data: T };

export async function getHome(locale: string): Promise<HomePayload | null> {
  try {
    const { data } = await apiFetch<Envelope<HomePayload>>("/home", { locale });
    return data;
  } catch (error) {
    console.error("[api] getHome failed:", error);
    return null;
  }
}

export async function getCategoryTree(locale: string): Promise<Category[]> {
  try {
    const { data } = await apiFetch<Envelope<Category[]>>("/categories", { locale });
    return data;
  } catch (error) {
    console.error("[api] getCategoryTree failed:", error);
    return [];
  }
}

export async function getSettings(locale: string): Promise<Settings | null> {
  try {
    const { data } = await apiFetch<Envelope<Settings>>("/settings", { locale });
    return data;
  } catch (error) {
    console.error("[api] getSettings failed:", error);
    return null;
  }
}
