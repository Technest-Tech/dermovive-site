<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\InteractsWithMediaUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Flat category representation. `children` is included when the relation is
 * loaded (e.g. the nested tree endpoint); `product_count` when counted.
 *
 * @mixin \App\Models\Category
 */
class CategoryResource extends JsonResource
{
    use InteractsWithMediaUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->firstMediaUrls($this->resource, 'image', [
                'thumb' => 'thumb',
                'preview' => 'preview',
            ]),
            'product_count' => $this->whenCounted('products'),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
