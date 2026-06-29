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

];
