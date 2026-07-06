<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Relationships
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();

            // Stock
            $table->integer('stock_quantity')->default(0)->unsigned();
            $table->integer('low_stock_threshold')->default(5)->unsigned();

            // Media
            $table->string('thumbnail')->nullable();

            // Physical attributes
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('weight_unit', 10)->nullable(); // kg, g, lb, oz
            $table->string('color', 50)->nullable();
            $table->string('size', 50)->nullable(); // ✅ Fixed typo

            // Status & Features
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

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
            $table->index('sku');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
