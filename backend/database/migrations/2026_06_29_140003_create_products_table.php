<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();

            // Translatable content
            $table->json('name');
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->json('ingredients')->nullable();   // INCI / ingredient text per locale
            $table->json('benefits')->nullable();       // array of strings per locale
            $table->json('how_to_use')->nullable();     // array of steps per locale

            // Pricing — display-only in Phase 1, ready for commerce later
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            // Merchandising
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('badges')->nullable(); // ['new','bestseller','limited'] (App\Enums\ProductBadge)

            // Canonical category (breadcrumb); multi-category via product_category pivot
            $table->foreignId('primary_category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // SEO (translatable)
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
