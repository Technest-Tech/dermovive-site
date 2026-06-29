<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            // Taxonomy group: skin_type | concern | highlight (see App\Enums\TagType).
            $table->string('type')->default('concern');
            $table->string('slug')->unique();
            $table->json('name'); // translatable
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
