<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Auto-generates a unique slug on create when none is provided.
 * The source value is taken from the model's default-locale translation
 * of `$slugSource` (defaults to "name"; set to "title" on Page).
 */
trait GeneratesSlug
{
    public static function bootGeneratesSlug(): void
    {
        static::creating(function ($model) {
            if (blank($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    public function generateUniqueSlug(): string
    {
        $field = property_exists($this, 'slugSource') ? $this->slugSource : 'name';
        $locale = config('dermovive.default_locale');

        $source = method_exists($this, 'getTranslation')
            ? $this->getTranslation($field, $locale, false)
            : $this->{$field};

        $base = Str::slug(is_string($source) && $source !== '' ? $source : Str::random(8));
        $slug = $base;
        $i = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
