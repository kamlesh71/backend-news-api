<?php

use App\Models\NewsCategory;
use App\Models\NewsSource;
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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid();
            $table->foreignIdFor(NewsCategory::class, 'news_category_id')->nullable();
            $table->foreignIdFor(NewsSource::class, 'news_source_id')->nullable();
            $table->string('data_source');
            $table->string('title', 1500);
            $table->string('image_url', 500)->nullable();
            $table->string('author')->nullable();
            $table->dateTime('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
