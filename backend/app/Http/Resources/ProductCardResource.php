<?php

namespace App\Http\Resources;

use App\Enums\ProductBadge;
use App\Http\Resources\Concerns\InteractsWithMediaUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight product representation for listings, grids and carousels.
 * The full detail payload is {@see ProductResource}, which extends this.
 *
 * @mixin \App\Models\Product
 */
class ProductCardResource extends JsonResource
{
    use InteractsWithMediaUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'price' => $this->price !== null ? (float) $this->price : null,
            'compare_at_price' => $this->compare_at_price !== null ? (float) $this->compare_at_price : null,
            'currency' => $this->currency,
            'is_featured' => (bool) $this->is_featured,
            'badges' => $this->badgePayload(),
            'primary_category' => $this->whenLoaded('primaryCategory', fn () => $this->primaryCategory ? [
                'id' => $this->primaryCategory->id,
                'slug' => $this->primaryCategory->slug,
                'name' => $this->primaryCategory->name,
            ] : null),
            'image' => $this->firstMediaUrls($this->resource, 'gallery', [
                'thumb' => 'thumb',
                'preview' => 'preview',
            ]),
        ];
    }

    /** @return array<int, array{value: string, label: string, color: string}> */
    protected function badgePayload(): array
    {
        return collect($this->badges ?? [])
            ->map(fn (ProductBadge $badge) => [
                'value' => $badge->value,
                'label' => $badge->getLabel(),
                'color' => $badge->getColor(),
            ])
            ->values()
            ->all();
    }
}
