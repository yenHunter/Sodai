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
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description', 255)->nullable();
            $table->longText('description')->nullable();

            // Relationships
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Media
            $table->string('thumbnail')->nullable();

            // Status & Features
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Denormalized price/stock cache, recalculated from variants
            // on every variant create/update/delete. Lets the product
            // index/listing sort & filter by price without joining variants.
            $table->decimal('min_price', 10, 2)->default(0);
            $table->decimal('max_price', 10, 2)->default(0);
            $table->integer('total_stock')->default(0)->unsigned();

            // Analytics
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0)->unsigned();
            $table->integer('total_sales')->default(0)->unsigned();

            // SEO
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category_id', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index('slug');
            $table->index(['min_price', 'max_price']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
