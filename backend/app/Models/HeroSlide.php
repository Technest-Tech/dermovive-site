<?php

namespace App\Models;

use App\Enums\LinkType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class HeroSlide extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'subtitle',
        'cta_label',
        'link_type',
        'link_target',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    public array $translatable = ['title', 'subtitle', 'cta_label'];

    protected $casts = [
        'link_type' => LinkType::class,
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')->width(1600)->nonQueued();
    }
}
