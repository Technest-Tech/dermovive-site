<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\CachesApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

/**
 * @group Content
 */
class PageController extends Controller
{
    use CachesApiResponses;

    /**
     * Get a page
     *
     * A published CMS page (title, HTML body, SEO meta) by slug.
     *
     * @urlParam slug string required The page slug. Example: our-story
     */
    public function show(string $slug): JsonResponse
    {
        $payload = $this->cachedPayload("page:{$slug}", function () use ($slug) {
            $page = Page::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            return (new PageResource($page))->response()->getData(true);
        });

        return response()->json($payload);
    }
}
