<?php

namespace App\Http\Resources\Concerns;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Builds absolute media URLs (original + conversions) for API payloads.
 *
 * Conversions are passed as [outputKey => conversionName]; if a conversion
 * has not been generated yet the original URL is returned for that key, so
 * the frontend always receives a usable image URL.
 */
trait InteractsWithMediaUrls
{
    /** URLs for the first item in a single-image collection, or null when empty. */
    protected function firstMediaUrls(HasMedia $model, string $collection, array $conversions = []): ?array
    {
        $media = $model->getFirstMedia($collection);

        return $media ? $this->urlsFor($media, $conversions) : null;
    }

    /**
     * URLs for every item in a collection (e.g. a product gallery).
     *
     * @return array<int, array<string, string>>
     */
    protected function mediaUrls(HasMedia $model, string $collection, array $conversions = []): array
    {
        return $model->getMedia($collection)
            ->map(fn (Media $media) => $this->urlsFor($media, $conversions))
            ->all();
    }

    /** @return array<string, string> */
    protected function urlsFor(Media $media, array $conversions): array
    {
        $urls = ['original' => $media->getFullUrl()];

        foreach ($conversions as $key => $conversion) {
            $urls[$key] = $media->hasGeneratedConversion($conversion)
                ? $media->getFullUrl($conversion)
                : $media->getFullUrl();
        }

        return $urls;
    }
}
