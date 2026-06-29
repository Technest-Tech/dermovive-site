<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\CachesApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\HeroSlideResource;
use App\Http\Resources\ProductCardResource;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * @group Storefront
 */
class HomeController extends Controller
{
    use CachesApiResponses;

    /**
     * Homepage
     *
     * Composite payload for the landing page: active `hero` slides (within their
     * schedule window, sorted), `featured` and `newest` product cards, and the
     * top-level `categories`.
     */
    public function index(): JsonResponse
    {
        $payload = $this->cachedPayload('home', function () {
            $now = now();

            $hero = HeroSlide::query()
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
                ->with('media')
                ->orderBy('sort_order')
                ->get();

            $featured = Product::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->with(['primaryCategory', 'media'])
                ->latest()
                ->take(8)
                ->get();

            $newest = Product::query()
                ->where('is_active', true)
                ->with(['primaryCategory', 'media'])
                ->latest()
                ->take(8)
                ->get();

            $categories = Category::query()
                ->where('is_active', true)
                ->whereIsRoot()
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->with('media')
                ->defaultOrder()
                ->get();

            return ['data' => [
                'hero' => HeroSlideResource::collection($hero)->resolve(request()),
                'featured' => ProductCardResource::collection($featured)->resolve(request()),
                'newest' => ProductCardResource::collection($newest)->resolve(request()),
                'categories' => CategoryResource::collection($categories)->resolve(request()),
            ]];
        });

        return response()->json($payload);
    }
}
