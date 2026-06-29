# Introduction

Public, read-only JSON API powering the Dermovive Pharma storefront.

<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>

    All endpoints are unauthenticated and return a consistent envelope: `{ "data": ... }`,
    with `links` and `meta` added on paginated listings.

    **Localisation.** Content is trilingual (English, Arabic, French). Send an
    `Accept-Language: en|ar|fr` header (or `?locale=` for quick checks) to pick the
    language; translatable fields are returned already resolved to that locale, and the
    response carries a matching `Content-Language` header. The default is English.

    <aside>Code examples for each endpoint appear in the dark area to the right; switch language with the tabs at the top right.</aside>

