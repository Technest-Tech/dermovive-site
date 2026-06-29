<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use GeneratesSlug;
    use HasTranslations;

    /** Slug is generated from the translatable title. */
    protected string $slugSource = 'title';

    protected $fillable = [
        'slug',
        'title',
        'body',
        'is_published',
        'meta_title',
        'meta_description',
    ];

    public array $translatable = ['title', 'body', 'meta_title', 'meta_description'];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
