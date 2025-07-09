<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicIdToPostImagesAndVideos extends Migration
{
    public function up()
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->string('public_id')->nullable()->after('image_url');
        });

        Schema::table('post_videos', function (Blueprint $table) {
            $table->string('public_id')->nullable()->after('video_url');
        });
    }

    public function down()
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });

        Schema::table('post_videos', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
}

