<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatalogSeeder::class);
        $this->admin = User::factory()->create();
    }

    public function test_catalog_was_seeded(): void
    {
        $this->assertSame(7, Category::count());
        $this->assertSame(7, Product::count());
        $this->assertSame(11, Tag::count());
        $this->assertSame(3, HeroSlide::count());
        $this->assertSame(2, Page::count());
    }

    public function test_category_tree_is_two_levels_deep(): void
    {
        $deepest = Category::query()->withDepth()->get()->max('depth');
        $this->assertSame(1, $deepest, 'Expected a 2-level tree (depth 0,1).');
    }

    public function test_all_admin_pages_render(): void
    {
        $this->actingAs($this->admin);

        $category = Category::query()->first();
        $product = Product::query()->first();
        $tag = Tag::query()->first();
        $slide = HeroSlide::query()->first();
        $page = Page::query()->first();
        $message = ContactMessage::create([
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'message' => 'A test contact message for the admin panel.',
            'locale' => 'en',
        ]);

        $urls = [
            '/admin',
            '/admin/categories',
            '/admin/categories/create',
            "/admin/categories/{$category->id}/edit",
            '/admin/products',
            '/admin/products/create',
            "/admin/products/{$product->id}/edit",
            '/admin/tags',
            '/admin/tags/create',
            "/admin/tags/{$tag->id}/edit",
            '/admin/hero-slides',
            '/admin/hero-slides/create',
            "/admin/hero-slides/{$slide->id}/edit",
            '/admin/pages',
            '/admin/pages/create',
            "/admin/pages/{$page->id}/edit",
            '/admin/manage-settings',
            '/admin/contact-messages',
            "/admin/contact-messages/{$message->id}",
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertSuccessful();
        }
    }
}
