<?php

namespace App\Http\Concerns;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

/**
 * Caches a JSON-serialisable API payload per locale for a short TTL.
 *
 * Keys are namespaced with the active locale so the same endpoint serves
 * distinct cache entries for en/ar/fr. A TTL of 0 bypasses the cache.
 */
trait CachesApiResponses
{
    protected function cachedPayload(string $key, callable $callback): mixed
    {
        $ttl = (int) config('dermovive.api_cache_ttl', 300);

        if ($ttl <= 0) {
            return $callback();
        }

        return Cache::remember('api:v1:'.App::getLocale().':'.$key, $ttl, $callback);
    }
}
