# Dermovive Pharma — Session Handoff

> Paste this whole file as the first message in a fresh session to continue the build.
> It is self-contained: project context, what exists, how to run it, conventions,
> environment gotchas, and the next task.

---

## 0) Your role & working style

You are continuing a multi-phase greenfield build. Work **one phase at a time**, verify
each phase (run it, screenshot it, write/extend tests), and **commit only when asked**.
The user (Ahmed) is a senior dev; be concise, recommend rather than enumerate options,
and don't re-litigate settled decisions. Approved plan lives at
`/Users/ahmedomar/.claude/plans/misty-rolling-pond.md`. Project memories live in
`~/.claude/projects/-Users-ahmedomar-Documents-technest-Dermovive/memory/` (see
`MEMORY.md` index — `dermovive-phase-2-api`, `mysql-json-search-collation`).

## 1) What the product is

**Dermovive Pharma** — a premium **trilingual cosmetics brand + product showcase** site
with an admin dashboard. Phase 1 is **browse-only** (no cart/checkout/payment/favorites/
shipping); those are **future** features, so the schema, API, and frontend are architected
to accept commerce later **without rework**. It should feel like a high-end e-commerce site
(hero slider with deals/products, browsable catalog, elegant product pages) but only shows
products for now.

**Locked decisions:**
- **Trilingual** content + UI: Arabic (RTL) + English + French (LTR), from day one.
- **Admin** = Filament 3 (Laravel-native).
- **Design** = elegant soft-luxury; coral `#E87A8E` + deep teal `#1C4A45`, serif display +
  clean sans, subtle motion. Logo at repo root `D.png` (a woman silhouette in coral + teal
  "Dermovive Pharma" wordmark).
- **Product model** is rich from day one (variants, ingredients, skin type, benefits,
  usage steps, gallery, badges).
- **Stack**: Next.js (App Router, TS) frontend · Laravel + Filament admin · MySQL. Decoupled/headless.

## 2) Repo, stack & exact versions

Repo: **`/Users/ahmedomar/Documents/technest/Dermovive`** — its own git repo (the home dir
`~/` is a *separate* git root; keep this isolated). Branch `main`. Commits so far:
`4257ee2` Phase 0, `7baf602` Phase 1, plus **Phase 2 (public API)** and **Phase 3 (data-driven
home + nav)** — see `git log`.

```
Dermovive/
├── backend/    Laravel 12.62 API + Filament 3 admin (PHP 8.2.28, Composer 2.8.9)
├── frontend/   Next.js 16.2.9 + React 19.2.4 + Tailwind v4 (Node 24.10, npm 11.6)
├── docker-compose.yml   optional mailpit only (DB is local MySQL, not Docker)
├── D.png       brand logo
├── README.md
└── HANDOFF.md  (this file)
```

Backend packages: `filament/filament ^3.2`, `filament/spatie-laravel-translatable-plugin ^3.2`,
`filament/spatie-laravel-media-library-plugin ^3.2`, `spatie/laravel-translatable ^6`,
`spatie/laravel-medialibrary ^11`, `spatie/laravel-permission ^6`, `kalnoy/nestedset ^6`,
`laravel/sanctum ^4`.

Frontend packages: `next-intl ^4.13`, `framer-motion ^12.42`, `embla-carousel-react ^8.6`,
`lucide-react ^1.22`, `clsx`, `tailwind-merge`.

## 3) How to run & credentials

**Database** — local MySQL 9.3 (Homebrew). DB `dermovive` (utf8mb4). User `new_user`,
password is in `backend/.env` (contains a `$`, so it's single-quoted there). Connection is
`127.0.0.1:3306`.

**Backend**
```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000   # admin: /admin · API: /api/v1
php artisan migrate:fresh --seed                  # rebuild + demo data
php artisan test                                  # uses in-memory sqlite (phpunit.xml)
php artisan optimize:clear                         # after route/config edits
```
Admin login: **`admin@dermovive.test`** / **`password`** (seeded). `canAccessPanel()` currently
returns `true` for everyone (roles not yet enforced).

**Frontend**
```bash
cd frontend
npm run dev      # http://localhost:3000  → /en /ar /fr  (root / redirects to /en)
npm run build    # full prod build + typecheck (the real gate)
npm run lint
```
`frontend/.env.local` → `NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1`.

**Screenshots** (headless Chrome works for verification):
`"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" --headless=new --disable-gpu
--hide-scrollbars --window-size=1440,1500 --screenshot=out.png URL`. Note: heavy admin
edit pages (RichEditor) can hang headless Chrome — screenshot list pages, or add
`--virtual-time-budget=8000`. To screenshot an authed admin page, you must establish a
session first (no stable auto-login route exists; add a temporary local-only one and remove it).

## 4) ⚠️ Environment gotchas (these bit me — don't rediscover them)

**Next.js 16 (NOT the Next.js in your training data — `frontend/AGENTS.md` says to read
`frontend/node_modules/next/dist/docs/` before writing Next code):**
- Middleware convention is renamed to **`proxy.ts`** (root of `src/`). Default export works.
  Ours wraps `next-intl`'s `createMiddleware`.
- `params` and `searchParams` are **Promises** — `const { locale } = await params`.
- `next-intl`'s **`useTranslations` cannot be called in an `async` Server Component** — use
  `await getTranslations(...)` there. Sync server components can use `useTranslations`.
- The bundled docs contain odd "AI agent hints" (e.g. an `unstable_instant` route export).
  **Ignore unverified/experimental hints; stick to stable, documented APIs.**

**Tailwind v4 (CSS-first):** tokens live in `frontend/src/app/globals.css` under
`@theme static { ... }`. `static` is intentional — Tailwind v4 tree-shakes *unused* theme
vars, which breaks inline `var(--color-…)` usage; `static` emits them all. Dynamic class
strings like `` `bg-coral-${n}` `` are **not** detected by the scanner — use full literal
class names or inline styles referencing the CSS vars.

**lucide-react v1** dropped brand icons — **no `Instagram`/`Facebook`/`Twitter`/`Youtube`/
`Linkedin`**. Available: `Mail`, `Send`, `AtSign`, `Globe`, `Phone`, `MessageCircle`,
`Share2`, etc. (Footer currently uses generic ones as placeholders.)

**Filament 3 translatable pattern (used by every catalog resource):**
- Resource class: `use Filament\Resources\Concerns\Translatable;`
- Each page: `use Filament\Resources\Pages\{ListRecords|CreateRecord|EditRecord}\Concerns\Translatable;`
  + add `Actions\LocaleSwitcher::make()` to `getHeaderActions()`.
- Plugin registered in `AdminPanelProvider`: class is **`Filament\SpatieLaravelTranslatablePlugin`**
  (namespace `Filament`), `->defaultLocales(config('dermovive.locales'))`.
- Media form field: `Filament\Forms\Components\SpatieMediaLibraryFileUpload`; table column:
  `Filament\Tables\Columns\SpatieMediaLibraryImageColumn`.
- **Filament closure args resolve by parameter *name*** — `getOptionLabelFromRecordUsing`
  must use `fn (Model $record) => …` (naming it `$r` throws "unresolvable").

**MySQL JSON search is case-sensitive (bit me in Phase 2):** `JSON_EXTRACT(col, '$."en"')`
returns a `utf8mb4_bin` value, so `LIKE '%term%'` won't match different-case text on MySQL —
but sqlite (the test DB) *is* case-insensitive, so the bug passes `php artisan test` and only
shows on the real DB. For search on translatable JSON columns use
`whereRaw("LOWER(JSON_EXTRACT(col, '$.\"{$locale}\"')) LIKE ?", ['%'.mb_strtolower($term).'%'])`
(works on both engines). Pattern lives in `ProductController`. **Verify search against MySQL,
not just the test suite.** (Memory: `mysql-json-search-collation`.)

**Other:** pivot table `product_category` is named **explicitly** in the `belongsToMany`
relations (Laravel's convention would be `category_product`); `product_tag` matches convention.
MySQL password has `$` → single-quote it in `.env`. Brand colors: coral `#E87A8E`, teal `#1C4A45`.

## 5) What exists — Phase 0 (frontend foundations) ✅

- `frontend/next.config.ts` wraps `next-intl` plugin + image `remotePatterns` for the API origin.
- i18n: `src/i18n/routing.ts` (locales `['en','ar','fr']`, default `en`, `localePrefix:'always'`,
  `rtlLocales:['ar']`, `dirFor()`), `navigation.ts`, `request.ts`; `src/proxy.ts`;
  `messages/{en,ar,fr}.json` (real copy, namespaces: common/nav/home/footer/designSystem).
- `src/app/[locale]/layout.tsx` = root layout: fonts via `next/font/google`
  (**Cormorant Garamond** display, **Manrope** body, **IBM Plex Sans Arabic**), `<html lang dir>`,
  `NextIntlClientProvider`, Header + Footer, `generateStaticParams`, `generateMetadata`,
  `setRequestLocale`.
- `src/app/[locale]/page.tsx` — impressive **static** home (hero with gradient orbs + monogram +
  floating chips, trust bar, category teasers, brand story, "coming soon" band). Hero is a
  placeholder; the **real data-driven slider is Phase 3**.
- `src/app/[locale]/design-system/page.tsx` — palette/type/buttons/cards showcase (async ⇒ uses
  `getTranslations`).
- Components: `layout/{Header,Footer,Logo,LanguageSwitcher}`, `ui/{Button,FadeIn}`.
- `lib/api.ts` (`apiFetch<T>(path,{locale,next})` → sets `Accept-Language`, base
  `NEXT_PUBLIC_API_URL`), `lib/utils.ts` (`cn`). Design tokens in `globals.css`.
- Verified: `/en /ar /fr` render with correct `dir`, localized content, all 3 fonts; build+lint pass.
- **Note:** Phase 0's static home labels (category teasers, trust bar, hero) were replaced by
  translated API data in Phase 3 (§7c). The page is now data-driven.

## 6) What exists — Phase 1 (catalog data model + Filament admin) ✅

**Config:** `backend/config/dermovive.php` → `locales ['en','ar','fr']`, `default_locale 'en'`,
`rtl_locales ['ar']`, `locale_labels`. Keep this in sync with the frontend `routing.ts`.

**Migrations** (`database/migrations/2026_06_29_14000X_*`):
`categories` (nested-set via `NestedSet::columns`, translatable name/description/meta, slug,
is_active) · `tags` (type, slug, translatable name) · `products` (slug, sku, translatable
name/short_description/description/ingredients/benefits/how_to_use/meta, price/compare_at_price/
currency, is_active, is_featured, badges JSON, primary_category_id, ) · `product_category` &
`product_tag` pivots · `product_variants` (name **plain string**, sku, price, sort_order,
is_default, is_active) · `hero_slides` (translatable title/subtitle/cta_label, link_type/target,
sort, active, starts_at/ends_at) · `pages` (slug, translatable title/body/meta, is_published) ·
`settings` (key/value JSON).

**Models** (`app/Models`): `Category` (HasTranslations + kalnoy `NodeTrait` + media `image` +
`GeneratesSlug`), `Product` (HasTranslations + media `gallery`; `badges` cast
`AsEnumCollection:ProductBadge`; relations `primaryCategory`, `categories`(→'product_category'),
`tags`, `variants`), `ProductVariant` (plain), `Tag` (HasTranslations; `type` cast `TagType`),
`HeroSlide` (HasTranslations + media `image`; `link_type` cast `LinkType`), `Page`
(HasTranslations; slug source = `title`), `Setting` (cached key/value with `get()/set()`).
Trait `app/Models/Concerns/GeneratesSlug.php` auto-slugs from the default-locale value on create.
Enums `app/Enums/{TagType,ProductBadge,LinkType}.php` (implement Filament `HasLabel`/`HasColor`).
Media conversions are `->nonQueued()` (no queue worker needed in dev).

**Filament** (`app/Filament`): `AdminPanelProvider` is branded (coral primary, teal gray, logo,
`brandName`, Poppins) + registers the translatable plugin. Resources: **Category** (indented,
cycle-safe parent picker; tree-ordered table), **Product** (tabbed form: General · Media ·
Categories & Tags · Details[benefits/how-to-use simple repeaters + ingredients] · Variants
[relationship repeater] · Pricing & SEO), **Tag**, **HeroSlide**, **Page**; plus a
`Pages/ManageSettings` page (+ `resources/views/filament/pages/manage-settings.blade.php`).
Nav groups: **Catalog** (Categories, Products, Tags & Filters) and **Content** (Hero Slides,
Pages, Site Settings).

**Seeder** `database/seeders/CatalogSeeder.php` (called by `DatabaseSeeder` after the admin user):
3-level tree (Skincare › Cleansers › Gentle Cleansers/Exfoliators; + Serums/Moisturisers/Sun Care;
Makeup › Face/Lips; Body), 11 tags, **8 products** (with trilingual content, variants, badges,
3 featured), 3 hero slides, 2 pages (Our Story, Contact), settings. No product images seeded.

**Tests** `tests/Feature/AdminPanelTest.php` (in-memory sqlite, RefreshDatabase + seed): asserts
seeded counts, 3-level tree depth, and that **every list/create/edit page + settings renders**
(`assertSuccessful`). `php artisan test` → all green.

**Deferred/known:** variant `name` is not translatable (sizes/shades); roles/permissions not yet
enforced; settings are not translatable yet; no product images in seed.

## 7) What exists — Phase 2 (Public Read API `/api/v1`) ✅

A documented, versioned, read-only JSON API the Next.js site consumes in Phase 3.
**16 passing tests** (`Feature/ApiTest`) + curl-verified against live MySQL in en/ar/fr.

**Locale negotiation:** `app/Http/Middleware/SetLocale.php`, on the `v1` group. Order =
`?locale=` query → `Accept-Language` header → default (query wins as an explicit override;
the frontend only sends the header, so its behaviour is unchanged). Sets `App::setLocale()` and a
`Content-Language` response header.

**Endpoints** (envelope `{ data, meta?, links? }`; `meta`/`links` added on paginated lists):
- `GET /home` → `{ hero[], featured[], newest[], categories[] }` (hero respects
  starts_at/ends_at + sort_order).
- `GET /categories` → full active nested tree (`children`, direct `product_count`).
- `GET /categories/{slug}` → `{ category, breadcrumbs, children, products }` (products span the
  category + all descendants).
- `GET /products` → filters `category` (incl. descendants), `tag`, `featured`, `q`, `sort`
  (newest/price/price_desc/name), `per_page` (1–48), `page`. Items are cards.
- `GET /products/{slug}` → full product (description, ingredients, benefits[], how_to_use[],
  gallery, variants, tags, categories, SEO meta, related[]).
- `GET /pages/{slug}`, `GET /settings`.

**Resources** in `app/Http/Resources`: `ProductResource extends ProductCardResource`;
`HeroSlideResource`, `CategoryResource`, `TagResource`, `ProductVariantResource`, `PageResource`.
Media URLs are absolute (`Concerns/InteractsWithMediaUrls`, `getFullUrl` + `thumb`/`preview`
conversions; `null`/`[]` when no media — none seeded yet). **Caching:**
`app/Http/Concerns/CachesApiResponses` — per-locale, TTL `config('dermovive.api_cache_ttl')`
(default 300s; 0 disables; tests use the `array` store so they're isolated). **Docs:**
`knuckleswtf/scribe` (dev dep), scoped to `api/v1/*`, served at `/docs`, `/docs.openapi`,
`/docs.postman`; regenerate with `php artisan scribe:generate`. The `.scribe/` source is
committed; generated views/assets are gitignored.

**Known/deferred:** category `product_count` is a **direct-attach** count (root categories read 0
since products attach to leaf categories) — make subtree-aware later if a teaser needs it. No
product images seeded, so `image`/`gallery` are null/empty.

## 7c) What exists — Phase 3 (Home + navigation, wired to the API) ✅

The home and chrome now render live `/api/v1` data in en/ar/fr. `npm run lint` + `npm run build`
pass; verified with screenshots in all 3 locales (LTR + RTL).

**Data layer** (`frontend/src/lib`): `types.ts` (API payload types), `queries.ts`
(`getHome`/`getCategoryTree`/`getSettings` — each try/catch → safe empty so the **build never
depends on the backend**), `format.ts` (`formatPrice` via `Intl.NumberFormat`, `discountPercent`,
`resolveHeroLink`). Fetchers go through the existing `apiFetch`.

**Home** (`src/app/[locale]/page.tsx`, async, fetches `/home`): `HeroSlider` → `TrustBar` (i18n) →
`ProductRail` (featured) → `CategoryTeasers` → `ProductRail` (newest) → `Story` (i18n).
- `components/home/HeroSlider.tsx` — Embla + manual autoplay (pauses on hover/focus/tab-hidden,
  respects reduced-motion) + Framer Motion text, RTL via `direction`, arrows/dots, dark-gradient
  fallback per slide + a full fallback hero when the API returns no slides.
- `components/home/ProductRail.tsx` — Embla carousel + nav arrows; `CategoryTeasers.tsx` — grid.
- `components/product/ProductCard.tsx` + `components/ui/MediaImage.tsx` (`next/image` with a seeded
  branded gradient placeholder — no imagery seeded yet).

**Chrome:** layout fetches the tree + settings once and passes them down.
`components/layout/MegaMenu.tsx` (desktop hover/focus dropdown from `/categories`),
`MobileMenu.tsx` (drawer), `Footer.tsx` Contact column wired to `/settings` (email/phone/address;
tagline/newsletter stay i18n since settings aren't translatable). Badge labels are localized by
`value` (`product.badge.*`) since the API's labels are English-only.

**i18n:** added `nav.shop/menu`, `home.{trust,slider,rails}`, `product.{badge,save}`,
`footer.contact` across all three message files.

**Rendering/known:** data-driven pages are **SSG** (baked at `next build`, so the backend was up
during the build; graceful fetchers bake fallback UI if it's down). Live data on every request via
`npm run dev`. **ISR/revalidation is Phase 6.** Forward links (mega-menu, teasers, product cards,
"View all") point at `/categories/{slug}`, `/products/{slug}`, `/products` — correct destinations
that **404 until Phase 4/5**. Embla added no new dep (manual autoplay).

## 7d) NEXT TASK — Phase 4: Catalog (category pages + product listing)

Build the browsable catalog the home links into. APIs are ready: `GET /categories/{slug}`
(`{ category, breadcrumbs, children, products }`) and `GET /products` (filters `category` incl.
descendants, `tag`, `featured`, `q`, `sort`, `per_page`, `page`; paginated `{ data, meta, links }`).

**Deliverables**
- **Category pages** `app/[locale]/categories/[slug]/page.tsx`: multi-level breadcrumbs (from
  `breadcrumbs`), subcategory grid (`children`), and a product grid. `params`/`searchParams` are
  **Promises** (await them — see §4).
- **Product listing** `app/[locale]/products/page.tsx`: grid of `ProductCard`s with **filters**
  (skin type/concern via `tag`, category), **sort**, **search** (`q`), and **pagination** (drive
  via URL `searchParams` so it's shareable/SSR-able). Add a `getProducts`/`getCategory` fetcher to
  `lib/queries.ts` returning the typed envelope.
- Reuse `ProductCard`, `MediaImage`, `formatPrice`, `dirFor`; keep filters RTL-aware.

**Conventions:** read `frontend/node_modules/next/dist/docs/` before writing Next 16 code (§4);
keep fetchers graceful; type payloads; don't hardcode the API origin (`NEXT_PUBLIC_API_URL`).

## 8) Remaining roadmap (after Phase 4 — see §7d for Phase 4)

- **Phase 5** — Product detail (gallery, variant selector display, ingredients/benefits/usage tabs,
  related; disabled "Add to cart"/"Wishlist" slots + "Inquire" CTA), company/portfolio pages
  (Our Story, certifications, contact form UI), full SEO (per-locale metadata, hreflang ar/en/fr,
  sitemap, Product structured data).
- **Phase 6** — Polish/perf (image optimization, ISR/tag revalidation, a11y, skeletons, 404),
  deploy (frontend Vercel/Node, backend Forge/VPS, media S3/local, CI).
- **Future (architected for, not built):** customer auth → wishlist → cart → checkout → payment →
  shipping/orders → reviews → inventory → promotions.

## 9) First moves in the fresh session

1. `cd backend && php artisan serve …` and `cd frontend && npm run dev` to confirm both boot;
   `php artisan migrate:fresh --seed` if the DB is empty; `php artisan test` should be green.
   Hit `/docs` and a few `/api/v1/*` endpoints (try `?locale=ar`) to see the live API shapes.
2. Read `frontend/src/lib/{api,queries,types,format}.ts`, `src/app/[locale]/page.tsx`,
   `src/components/{home,product}/*`, and the `/products` + `/categories/{slug}` payloads.
3. Build Phase 4 (§7d), verify by running the site + screenshots, then report and ask before
   starting Phase 5.
```
