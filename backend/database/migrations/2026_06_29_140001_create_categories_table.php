<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // Nested-set columns: _lft, _rgt, parent_id (multi-level tree).
            NestedSet::columns($table);

            $table->string('slug')->unique();
            $table->json('name');                 // translatable
            $table->json('description')->nullable(); // translatable
            $table->boolean('is_active')->default(true);

            // SEO (translatable)
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
