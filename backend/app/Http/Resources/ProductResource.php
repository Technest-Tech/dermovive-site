<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Full product detail. Extends {@see ProductCardResource} so it inherits the
 * card fields (name, pricing, badges, primary category, primary image) and
 * adds the rich content, gallery, variants, taxonomy, SEO meta and related
 * products. `related` is exposed by the controller via setRelation().
 *
 * @mixin \App\Models\Product
 */
class ProductResource extends ProductCardResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'sku' => $this->sku,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'benefits' => $this->localizedList('benefits'),
            'how_to_use' => $this->localizedList('how_to_use'),
            'gallery' => $this->mediaUrls($this->resource, 'gallery', [
                'thumb' => 'thumb',
                'preview' => 'preview',
            ]),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ],
            'related' => ProductCardResource::collection($this->whenLoaded('related')),
        ]);
    }

    /**
     * Benefits / how-to-use are stored as a per-locale array of strings.
     * Guard against missing translations (which resolve to an empty string).
     *
     * @return array<int, string>
     */
    protected function localizedList(string $attribute): array
    {
        $value = $this->{$attribute};

        if (is_array($value)) {
            return array_values($value);
        }

        return empty($value) ? [] : [$value];
    }
}
