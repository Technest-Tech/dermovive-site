<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\CachesApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductCardResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * @group Catalog
 */
class CategoryController extends Controller
{
    use CachesApiResponses;

    /**
     * List categories (tree)
     *
     * The full active category tree, nested via `children`, with a direct
     * `product_count` on each node.
     */
    public function index(): JsonResponse
    {
        $payload = $this->cachedPayload('categories:tree', function () {
            $tree = Category::query()
                ->where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->with('media')
                ->defaultOrder()
                ->get()
                ->toTree();

            return ['data' => CategoryResource::collection($tree)->resolve(request())];
        });

        return response()->json($payload);
    }

    /**
     * Get a category
     *
     * A single category with `breadcrumbs` (ancestors), direct `children`, and
     * `products` drawn from the category and all of its descendants.
     *
     * @urlParam slug string required The category slug. Example: serums
     */
    public function show(string $slug): JsonResponse
    {
        $payload = $this->cachedPayload("category:{$slug}", function () use ($slug) {
            $category = Category::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with('media')
                ->firstOrFail();

            $ancestors = $category->ancestors()
                ->where('is_active', true)
                ->defaultOrder()
                ->get();

            $children = $category->children()
                ->where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->with('media')
                ->defaultOrder()
                ->get();

            $descendantIds = $category->descendants()->pluck('id')->push($category->id);

            $products = Product::query()
                ->where('is_active', true)
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $descendantIds))
                ->with(['primaryCategory', 'media'])
                ->latest()
                ->take(24)
                ->get();

            return ['data' => [
                'category' => (new CategoryResource($category))->resolve(request()),
                'breadcrumbs' => CategoryResource::collection($ancestors)->resolve(request()),
                'children' => CategoryResource::collection($children)->resolve(request()),
                'products' => ProductCardResource::collection($products)->resolve(request()),
            ]];
        });

        return response()->json($payload);
    }
}
