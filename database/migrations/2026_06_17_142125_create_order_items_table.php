<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->string('product_name');        // snapshot
            $table->string('product_sku');         // snapshot (variant SKU)
            $table->string('product_image')->nullable();
            $table->string('variant_options')->nullable(); // snapshot, e.g. "Color: Red, Size: M"
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
