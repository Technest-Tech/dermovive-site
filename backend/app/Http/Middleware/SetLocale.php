<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Negotiates the request locale for the public API so translatable models
 * (categories, products, pages, hero slides) return the right language.
 *
 * Resolution order: explicit ?locale= query → Accept-Language header → default.
 * The frontend drives content via Accept-Language (it never sends ?locale=),
 * so an explicit ?locale= is treated as a stronger, intentional override for
 * direct/debug access where the browser would otherwise pin Accept-Language.
 * Only locales listed in config('dermovive.locales') are honoured.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('dermovive.locales', ['en']);
        $default = config('dermovive.default_locale', 'en');

        $locale = $this->fromQuery($request, $supported)
            ?? $this->fromHeader($request, $supported)
            ?? $default;

        App::setLocale($locale);

        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    /** First supported locale in the Accept-Language header, honouring q-weights. */
    protected function fromHeader(Request $request, array $supported): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $base = strtolower(explode('_', str_replace('-', '_', $language))[0]);

            if (in_array($base, $supported, true)) {
                return $base;
            }
        }

        return null;
    }

    /** Explicit ?locale= override, validated against the supported list. */
    protected function fromQuery(Request $request, array $supported): ?string
    {
        $locale = $request->query('locale');

        return is_string($locale) && in_array($locale, $supported, true) ? $locale : null;
    }
}
