<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reel_likes', function (Blueprint $table) {
            $table->foreignId('reel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['reel_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reel_likes');
    }
};
