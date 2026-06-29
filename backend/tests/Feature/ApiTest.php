<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_health_reports_negotiated_locale(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonPath('data.locale', 'en');

        $this->getJson('/api/v1/health', ['Accept-Language' => 'ar'])
            ->assertOk()
            ->assertJsonPath('data.locale', 'ar');
    }

    public function test_categories_returns_nested_active_tree(): void
    {
        $res = $this->getJson('/api/v1/categories')->assertOk();

        $res->assertJsonStructure([
            'data' => [['id', 'slug', 'name', 'product_count', 'children']],
        ]);

        $skincare = collect($res->json('data'))->firstWhere('slug', 'skincare');
        $this->assertNotEmpty($skincare['children']);

        // Three levels deep: Skincare › Cleansers › Gentle Cleansers.
        $cleansers = collect($skincare['children'])->firstWhere('slug', 'cleansers');
        $this->assertNotEmpty($cleansers['children']);
    }

    public function test_category_show_has_breadcrumbs_children_and_products(): void
    {
        $res = $this->getJson('/api/v1/categories/serums')->assertOk();

        $res->assertJsonStructure([
            'data' => [
                'category' => ['slug', 'name'],
                'breadcrumbs',
                'children',
                'products' => [['slug', 'name', 'price']],
            ],
        ]);

        $this->assertSame('skincare', $res->json('data.breadcrumbs.0.slug'));
        $this->assertNotEmpty($res->json('data.products'));
    }

    public function test_products_index_envelope_and_pagination(): void
    {
        $res = $this->getJson('/api/v1/products')->assertOk();

        $res->assertJsonStructure([
            'data' => [['id', 'slug', 'name', 'price', 'currency', 'badges', 'image']],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);

        $this->assertSame(8, $res->json('meta.total'));
    }

    public function test_products_filters(): void
    {
        $this->assertSame(3, $this->getJson('/api/v1/products?featured=1')->json('meta.total'));

        // Skincare subtree (serums, cleansers, moisturisers, suncare, exfoliators).
        $this->assertSame(6, $this->getJson('/api/v1/products?category=skincare')->json('meta.total'));

        $this->assertGreaterThan(0, $this->getJson('/api/v1/products?tag=vegan')->json('meta.total'));

        $unknown = $this->getJson('/api/v1/products?category=does-not-exist')->assertOk();
        $this->assertSame(0, $unknown->json('meta.total'));
    }

    public function test_products_search_and_sort(): void
    {
        // Case-insensitive name match (EN).
        $this->assertSame(2, $this->getJson('/api/v1/products?q=SERUM')->json('meta.total'));

        // Matches short_description, not just name.
        $this->assertSame(1, $this->getJson('/api/v1/products?q=cream')->json('meta.total'));

        // Localised search: Arabic term against the AR translation.
        $ar = $this->getJson('/api/v1/products?q='.urlencode('سيروم'), ['Accept-Language' => 'ar']);
        $this->assertSame(2, $ar->json('meta.total'));

        $sorted = $this->getJson('/api/v1/products?sort=price')->assertOk();
        $prices = collect($sorted->json('data'))->pluck('price')->all();
        $this->assertSame($prices, collect($prices)->sort()->values()->all());
    }

    public function test_product_show_full_payload(): void
    {
        $res = $this->getJson('/api/v1/products/hydra-glow-serum')->assertOk();

        $res->assertJsonStructure([
            'data' => [
                'slug', 'sku', 'description', 'ingredients', 'benefits', 'how_to_use',
                'gallery', 'variants' => [['name', 'sku', 'is_default']],
                'tags', 'categories', 'meta' => ['title', 'description'], 'related',
            ],
        ]);

        $this->assertIsArray($res->json('data.benefits'));
        $this->assertCount(2, $res->json('data.variants'));
    }

    public function test_translatable_fields_resolve_per_locale(): void
    {
        $slug = 'hydra-glow-serum';

        $this->assertSame(
            'Hydra-Glow Serum',
            $this->getJson("/api/v1/products/{$slug}", ['Accept-Language' => 'en'])->json('data.name')
        );
        $this->assertSame(
            'سيروم هيدرا غلو',
            $this->getJson("/api/v1/products/{$slug}", ['Accept-Language' => 'ar'])->json('data.name')
        );
        $this->assertSame(
            'Sérum Hydra-Glow',
            $this->getJson("/api/v1/products/{$slug}", ['Accept-Language' => 'fr'])->json('data.name')
        );

        // ?locale= fallback when no Accept-Language header is sent.
        $this->assertSame(
            'سيروم هيدرا غلو',
            $this->getJson("/api/v1/products/{$slug}?locale=ar")->json('data.name')
        );
    }

    public function test_home_composite_payload(): void
    {
        $res = $this->getJson('/api/v1/home')->assertOk();

        $res->assertJsonStructure([
            'data' => ['hero', 'featured', 'newest', 'categories'],
        ]);

        $this->assertCount(3, $res->json('data.hero'));
        $this->assertNotEmpty($res->json('data.featured'));
        $this->assertNotEmpty($res->json('data.categories'));
    }

    public function test_page_and_settings_endpoints(): void
    {
        $this->getJson('/api/v1/pages/our-story')
            ->assertOk()
            ->assertJsonPath('data.slug', 'our-story');

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('data.site_name', 'Dermovive Pharma');
    }

    public function test_missing_resources_return_404(): void
    {
        $this->getJson('/api/v1/products/nope')->assertNotFound();
        $this->getJson('/api/v1/categories/nope')->assertNotFound();
        $this->getJson('/api/v1/pages/nope')->assertNotFound();
    }
}
