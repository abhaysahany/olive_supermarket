<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('sub_category_id')->nullable()->constrained()->nullOnDelete()->after('category_id');
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('size')->nullable()->after('description');
            $table->string('short_size')->nullable()->after('size');
            $table->decimal('old_price', 10, 2)->nullable()->after('price');
            $table->integer('save_pct')->nullable()->after('old_price');
            $table->string('emoji')->nullable()->after('save_pct');
            $table->string('tint')->nullable()->after('emoji');
            $table->string('tag')->nullable()->after('tint');
            $table->decimal('rating', 3, 2)->default(0)->after('tag');
            $table->integer('reviews_count')->default(0)->after('rating');
            $table->boolean('local')->default(true)->after('reviews_count');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn([
                'sub_category_id', 'slug', 'size', 'short_size', 
                'old_price', 'save_pct', 'emoji', 'tint', 'tag', 
                'rating', 'reviews_count', 'local'
            ]);
        });
    }
};
