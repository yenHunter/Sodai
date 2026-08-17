<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_target', 10)->default('_self'); // _self | _blank
            $table->string('image');
            $table->string('mobile_image')->nullable();
            $table->string('position')->default('home_slider'); // home_slider, home_promo, category_banner, popup
            $table->string('text_position', 10)->default('left'); // left, center, right
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['position', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};