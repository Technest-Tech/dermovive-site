<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TagType;
use App\Http\Concerns\CachesApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

/**
 * @group Catalog
 */
class TagController extends Controller
{
    use CachesApiResponses;

    /**
     * List filter tags
     *
     * Active tags that are attached to at least one active product, grouped by
     * type (skin type, concern, highlight) in a stable order. Powers the catalog
     * filter UI; tag names are resolved to the negotiated locale. Group `label`
     * is English — the storefront localises headings by `type`.
     */
    public function index(): JsonResponse
    {
        $payload = $this->cachedPayload('tags:grouped', function () {
            $tags = Tag::query()
                ->where('is_active', true)
                ->whereHas('products', fn ($q) => $q->where('is_active', true))
                ->get()
                ->groupBy(fn (Tag $tag) => $tag->type?->value);

            // Stable, meaningful order for the filter sections.
            $order = [TagType::SkinType, TagType::Concern, TagType::Highlight];

            $groups = Collection::make($order)
                ->map(function (TagType $type) use ($tags) {
                    $items = $tags->get($type->value);

                    if (! $items || $items->isEmpty()) {
                        return null;
                    }

                    return [
                        'type' => $type->value,
                        'label' => $type->getLabel(),
                        'tags' => TagResource::collection(
                            $items->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values()
                        )->resolve(request()),
                    ];
                })
                ->filter()
                ->values()
                ->all();

            return ['data' => $groups];
        });

        return response()->json($payload);
    }
}
