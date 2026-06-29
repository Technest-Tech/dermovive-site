<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | Drives translatable content (categories, products, pages, hero slides),
    | Filament locale tabs, and API locale negotiation. Keep this list in sync
    | with the frontend's next-intl locales.
    |
    */

    'locales' => ['en', 'ar', 'fr'],

    'default_locale' => 'en',

    // Locales rendered right-to-left.
    'rtl_locales' => ['ar'],

    // Human labels for the admin locale switcher / API metadata.
    'locale_labels' => [
        'en' => 'English',
        'ar' => 'العربية',
        'fr' => 'Français',
    ],

    /*
    |--------------------------------------------------------------------------
    | Public API response cache
    |--------------------------------------------------------------------------
    |
    | TTL (seconds) for cached read-only /api/v1 responses. Keys are namespaced
    | per locale + query string. Set to 0 to disable (handy in local debugging;
    | tests run on the array store so they are isolated regardless).
    |
    */

    'api_cache_ttl' => (int) env('API_CACHE_TTL', 300),

];
