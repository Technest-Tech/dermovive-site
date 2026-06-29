<?php

namespace App\Models;

use App\Enums\TagType;
use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use GeneratesSlug;
    use HasTranslations;

    protected $fillable = ['type', 'slug', 'name', 'is_active'];

    public array $translatable = ['name'];

    protected $casts = [
        'type' => TagType::class,
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
