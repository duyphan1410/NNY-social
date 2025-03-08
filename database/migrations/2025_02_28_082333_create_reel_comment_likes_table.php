<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reel_comment_likes', function (Blueprint $table) {
            $table->foreignId('comment_id')->constrained('reel_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['comment_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reel_comment_likes');
    }
};
