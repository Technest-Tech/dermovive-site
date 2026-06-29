<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use GeneratesSlug;
    use HasTranslations;
    use InteractsWithMedia;
    use NodeTrait;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'parent_id',
        'meta_title',
        'meta_description',
    ];

    public array $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 400, 400)->nonQueued();
        $this->addMediaConversion('preview')->width(900)->nonQueued();
    }
}
