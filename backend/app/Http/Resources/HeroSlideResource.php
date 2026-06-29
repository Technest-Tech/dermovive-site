<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\InteractsWithMediaUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\HeroSlide
 */
class HeroSlideResource extends JsonResource
{
    use InteractsWithMediaUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'cta_label' => $this->cta_label,
            'link' => [
                'type' => $this->link_type?->value,
                'target' => $this->link_target,
            ],
            'image' => $this->firstMediaUrls($this->resource, 'image', [
                'preview' => 'preview',
            ]),
        ];
    }
}
