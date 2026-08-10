<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('sku')->unique();

            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();

            // Stock
            $table->integer('stock_quantity')->default(0)->unsigned();
            $table->integer('low_stock_threshold')->default(5)->unsigned();

            // Physical attributes (weight lives here since it's variant-specific,
            // e.g. Nescafé 100g vs 250g jars)
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('weight_unit', 10)->nullable();

            // Variant-specific image override (e.g. red iPhone thumbnail)
            $table->string('thumbnail')->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_active']);
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};