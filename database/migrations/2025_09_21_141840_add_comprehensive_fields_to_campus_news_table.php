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
        Schema::table('campus_news', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->text('content')->nullable()->after('title');
            $table->text('excerpt')->nullable()->after('content');
            $table->string('featured_image')->nullable()->after('excerpt');
            $table->json('gallery_images')->nullable()->after('featured_image');
            $table->date('publish_date')->nullable()->after('gallery_images');
            $table->date('event_date')->nullable()->after('publish_date');
            $table->dateTime('event_time')->nullable()->after('event_date');
            $table->string('location')->nullable()->after('event_time');
            $table->string('category')->nullable()->default('announcement')->after('location');
            $table->string('status')->nullable()->default('draft')->after('category');
            $table->boolean('is_featured')->nullable()->default(false)->after('status');
            $table->integer('views_count')->nullable()->default(0)->after('is_featured');
            $table->string('author_name')->nullable()->after('views_count');
            $table->unsignedBigInteger('author_id')->nullable()->after('author_name');
            $table->json('tags')->nullable()->after('author_id');

            // Add foreign key constraint for author_id
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_news', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn([
                'title',
                'content',
                'excerpt',
                'featured_image',
                'gallery_images',
                'publish_date',
                'event_date',
                'event_time',
                'location',
                'category',
                'status',
                'is_featured',
                'views_count',
                'author_name',
                'author_id',
                'tags'
            ]);
        });
    }
};
