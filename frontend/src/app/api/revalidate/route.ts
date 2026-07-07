import { NextResponse, type NextRequest } from "next/server";
import { revalidateTag } from "next/cache";

/**
 * On-demand cache invalidation webhook. The backend (or an admin action) POSTs
 * here after content changes to bust the matching cache tags — pages then serve
 * stale-while-revalidate until refreshed. Tags mirror `src/lib/cache.ts`.
 *
 * Auth: a shared secret via the `x-revalidate-secret` header or `?secret=`.
 * Body: `{ "tag": "products" }` or `{ "tags": ["products", "product:foo"] }`.
 */
export async function POST(request: NextRequest) {
  const secret = process.env.REVALIDATE_SECRET;
  const provided =
    request.headers.get("x-revalidate-secret") ??
    request.nextUrl.searchParams.get("secret");

  if (!secret || provided !== secret) {
    return NextResponse.json(
      { ok: false, message: "Invalid or missing secret." },
      { status: 401 },
    );
  }

  let body: { tag?: string; tags?: string[] } = {};
  try {
    body = await request.json();
  } catch {
    // Empty/invalid body — fall through to the validation below.
  }

  const list = body.tags ?? (body.tag ? [body.tag] : []);
  if (list.length === 0) {
    return NextResponse.json(
      { ok: false, message: "Provide `tag` or `tags`." },
      { status: 400 },
    );
  }

  for (const tag of list) {
    revalidateTag(tag, "max");
  }

  return NextResponse.json({ ok: true, revalidated: list });
}
