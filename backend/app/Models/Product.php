<?php

namespace App\Models;

use App\Enums\ProductBadge;
use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use GeneratesSlug;
    use HasTranslations;
    use InteractsWithMedia;

    protected $fillable = [
        'slug',
        'sku',
        'name',
        'short_description',
        'description',
        'ingredients',
        'benefits',
        'how_to_use',
        'price',
        'compare_at_price',
        'currency',
        'is_active',
        'is_featured',
        'badges',
        'primary_category_id',
        'meta_title',
        'meta_description',
    ];

    public array $translatable = [
        'name',
        'short_description',
        'description',
        'ingredients',
        'benefits',
        'how_to_use',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'badges' => AsEnumCollection::class.':'.ProductBadge::class,
    ];

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 500, 625)->nonQueued();
        $this->addMediaConversion('preview')->width(1000)->nonQueued();
    }
}
