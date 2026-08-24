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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // privacy-policy, terms-conditions, shipping-policy, return-refund-policy
            $table->string('title');
            $table->longText('content')->nullable();

            // Basic per-page SEO, consistent with the Settings > Marketing group pattern
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->foreignId('updated_by')->nullable()
                ->constrained('admins')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
