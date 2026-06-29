<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\CachesApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * @group Catalog
 */
class ProductController extends Controller
{
    use CachesApiResponses;

    /**
     * List products
     *
     * Paginated, filterable product listing for catalog grids and carousels.
     * Each item is a lightweight "card" (name, pricing, badges, primary
     * category, primary image).
     *
     * @queryParam category string Filter by category slug; includes all descendant categories. Example: skincare
     * @queryParam tag string Filter by tag slug (skin type, concern or highlight). Example: vegan
     * @queryParam featured boolean Return only featured products. Example: 1
     * @queryParam q string Search the localised name and short description. Example: serum
     * @queryParam sort string Sort order: `newest` (default), `price`, `price_desc`, `name`. Example: price
     * @queryParam per_page integer Items per page, 1–48 (default 12). Example: 12
     * @queryParam page integer Page number. Example: 1
     */
    public function index(Request $request): JsonResponse
    {
        $key = 'products:'.md5((string) $request->getQueryString());

        $payload = $this->cachedPayload($key, function () use ($request) {
            $locale = App::getLocale();

            $query = Product::query()
                ->where('is_active', true)
                ->with(['primaryCategory', 'media']);

            $this->applyCategoryFilter($query, $request->query('category'));

            if ($tag = $request->query('tag')) {
                $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
            }

            if ($request->boolean('featured')) {
                $query->where('is_featured', true);
            }

            if ($term = trim((string) $request->query('q'))) {
                // MySQL's JSON_EXTRACT returns utf8mb4_bin values, so LIKE is
                // case-sensitive; LOWER() on both sides keeps search case-
                // insensitive and works identically on sqlite (tests). $locale
                // is validated to en/ar/fr by SetLocale, so the path is safe.
                $like = '%'.mb_strtolower($term).'%';

                $query->where(function ($q) use ($like, $locale) {
                    $q->whereRaw("LOWER(JSON_EXTRACT(name, '$.\"{$locale}\"')) LIKE ?", [$like])
                        ->orWhereRaw("LOWER(JSON_EXTRACT(short_description, '$.\"{$locale}\"')) LIKE ?", [$like]);
                });
            }

            match ($request->query('sort')) {
                'price' => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'name' => $query->orderBy("name->{$locale}"),
                default => $query->latest(),
            };

            $perPage = min(max((int) $request->query('per_page', 12), 1), 48);

            $products = $query->paginate($perPage)->withQueryString();

            return ProductCardResource::collection($products)->response()->getData(true);
        });

        return response()->json($payload);
    }

    /**
     * Get a product
     *
     * Full product detail: rich content, gallery, variants, tags, categories,
     * SEO meta and related products from the same primary category.
     *
     * @urlParam slug string required The product slug. Example: hydra-glow-serum
     */
    public function show(string $slug): JsonResponse
    {
        $payload = $this->cachedPayload("product:{$slug}", function () use ($slug) {
            $product = Product::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with([
                    'primaryCategory',
                    'categories',
                    'tags',
                    'media',
                    'variants' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                ])
                ->firstOrFail();

            $related = Product::query()
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->where('primary_category_id', $product->primary_category_id)
                ->with(['primaryCategory', 'media'])
                ->latest()
                ->take(4)
                ->get();

            $product->setRelation('related', $related);

            return (new ProductResource($product))->response()->getData(true);
        });

        return response()->json($payload);
    }

    /** Scope the query to a category slug and all of its descendants. */
    protected function applyCategoryFilter($query, ?string $slug): void
    {
        if (! $slug) {
            return;
        }

        $category = Category::where('slug', $slug)->where('is_active', true)->first();

        if (! $category) {
            $query->whereRaw('1 = 0'); // unknown category → empty result set

            return;
        }

        $ids = $category->descendants()->pluck('id')->push($category->id);

        $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $ids));
    }
}
