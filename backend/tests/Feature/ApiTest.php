<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
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

        $cosmetics = collect($res->json('data'))->firstWhere('slug', 'cosmetics-skincare');
        $this->assertNotEmpty($cosmetics['children']);

        $childSlugs = collect($cosmetics['children'])->pluck('slug')->all();
        $this->assertContains('cleansers', $childSlugs);
        $this->assertContains('face-care', $childSlugs);
        $this->assertContains('sun-care', $childSlugs);
        $this->assertContains('body-care', $childSlugs);
    }

    public function test_category_show_has_breadcrumbs_children_and_products(): void
    {
        $res = $this->getJson('/api/v1/categories/face-care')->assertOk();

        $res->assertJsonStructure([
            'data' => [
                'category' => ['slug', 'name'],
                'breadcrumbs',
                'children',
                'products' => [['slug', 'name', 'price']],
            ],
        ]);

        $this->assertSame('cosmetics-skincare', $res->json('data.breadcrumbs.0.slug'));
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

        $this->assertSame(7, $res->json('meta.total'));
    }

    public function test_products_filters(): void
    {
        $this->assertSame(3, $this->getJson('/api/v1/products?featured=1')->json('meta.total'));

        // The Cosmetics & Skincare subtree contains the full current catalog.
        $this->assertSame(7, $this->getJson('/api/v1/products?category=cosmetics-skincare')->json('meta.total'));

        $this->assertSame(6, $this->getJson('/api/v1/products?tag=dermatologist-tested')->json('meta.total'));

        $unknown = $this->getJson('/api/v1/products?category=does-not-exist')->assertOk();
        $this->assertSame(0, $unknown->json('meta.total'));
    }

    public function test_products_search_and_sort(): void
    {
        // Case-insensitive name match (EN).
        $this->assertSame(2, $this->getJson('/api/v1/products?q=CLARIFYING')->json('meta.total'));

        // Matches short_description, not just name.
        $this->assertSame(1, $this->getJson('/api/v1/products?q=foaming')->json('meta.total'));

        // Localised search: Arabic term against the AR translation.
        $ar = $this->getJson('/api/v1/products?q='.urlencode('كريم'), ['Accept-Language' => 'ar']);
        $this->assertSame(4, $ar->json('meta.total'));

        $sorted = $this->getJson('/api/v1/products?sort=price')->assertOk();
        $prices = collect($sorted->json('data'))->pluck('price')->all();
        $this->assertSame($prices, collect($prices)->sort()->values()->all());
    }

    public function test_tags_grouped_by_type(): void
    {
        $res = $this->getJson('/api/v1/tags')->assertOk();

        $res->assertJsonStructure([
            'data' => [['type', 'label', 'tags' => [['id', 'type', 'slug', 'name']]]],
        ]);

        $types = collect($res->json('data'))->pluck('type')->all();
        $this->assertContains('skin_type', $types);
        $this->assertContains('concern', $types);

        // Skin type comes before concern (stable section order).
        $this->assertLessThan(
            array_search('concern', $types, true),
            array_search('skin_type', $types, true),
        );

        // Names resolve to the negotiated locale.
        $ar = $this->getJson('/api/v1/tags', ['Accept-Language' => 'ar'])->json('data');
        $arTags = collect($ar)->pluck('tags')->flatten(1)->pluck('name');
        $this->assertTrue($arTags->contains('الترطيب'));
    }

    public function test_product_show_full_payload(): void
    {
        $res = $this->getJson('/api/v1/products/clarifying-day-cream')->assertOk();

        $res->assertJsonStructure([
            'data' => [
                'slug', 'sku', 'description', 'ingredients', 'benefits', 'how_to_use',
                'gallery', 'variants' => [['name', 'sku', 'is_default']],
                'tags', 'categories', 'meta' => ['title', 'description'], 'related',
            ],
        ]);

        $this->assertIsArray($res->json('data.benefits'));
        $this->assertCount(1, $res->json('data.variants'));
    }

    public function test_translatable_fields_resolve_per_locale(): void
    {
        $slug = 'clarifying-day-cream';

        $this->assertSame(
            'Clarifying Day Cream',
            $this->getJson("/api/v1/products/{$slug}", ['Accept-Language' => 'en'])->json('data.name')
        );
        $this->assertSame(
            'كريم التفتيح النهاري',
            $this->getJson("/api/v1/products/{$slug}", ['Accept-Language' => 'ar'])->json('data.name')
        );
        $this->assertSame(
            'Crème Clarifiante Jour',
            $this->getJson("/api/v1/products/{$slug}", ['Accept-Language' => 'fr'])->json('data.name')
        );

        // ?locale= fallback when no Accept-Language header is sent.
        $this->assertSame(
            'كريم التفتيح النهاري',
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

    public function test_contact_message_is_validated_and_stored(): void
    {
        $payload = [
            'name' => 'Amina Ndiaye',
            'email' => 'amina@example.com',
            'phone' => '+221 77 000 0000',
            'subject' => 'Product question',
            'message' => 'I would like help choosing the right skincare routine.',
        ];

        $this->postJson('/api/v1/contact', $payload)
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'message']]);

        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'amina@example.com',
            'locale' => 'en',
            'is_read' => false,
        ]);

        $this->postJson('/api/v1/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => 'short',
        ])->assertUnprocessable()->assertJsonValidationErrors([
            'name',
            'email',
            'message',
        ]);
    }

    public function test_missing_resources_return_404(): void
    {
        $this->getJson('/api/v1/products/nope')->assertNotFound();
        $this->getJson('/api/v1/categories/nope')->assertNotFound();
        $this->getJson('/api/v1/pages/nope')->assertNotFound();
    }
}
