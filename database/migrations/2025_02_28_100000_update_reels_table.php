<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reels', function (Blueprint $table) {
            if (!Schema::hasColumn('reels', 'caption')) {
                $table->string('caption')->nullable();
            }
            if (!Schema::hasColumn('reels', 'duration')) {
                $table->integer('duration')->nullable();
            }
            if (!Schema::hasColumn('reels', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('reels', 'is_public')) {
                $table->boolean('is_public')->default(true);
            }
            if (!Schema::hasColumn('reels', 'views_count')) {
                $table->integer('views_count')->default(0);
            }
            if (!Schema::hasColumn('reels', 'likes_count')) {
                $table->integer('likes_count')->default(0);
            }
            if (!Schema::hasColumn('reels', 'comments_count')) {
                $table->integer('comments_count')->default(0);
            }
            if (!Schema::hasColumn('reels', 'shares_count')) {
                $table->integer('shares_count')->default(0);
            }
            if (!Schema::hasColumn('reels', 'audio_id')) {
                $table->unsignedBigInteger('audio_id')->nullable();
            }
        });
    }


    public function down()
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->dropColumn(['caption', 'duration', 'status', 'is_public', 'views_count', 'likes_count', 'comments_count', 'shares_count', 'audio_id']);
        });
    }
};
