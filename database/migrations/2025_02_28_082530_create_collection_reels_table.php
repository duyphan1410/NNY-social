<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('collection_reels', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained('reel_collections')->cascadeOnDelete();
            $table->foreignId('reel_id')->constrained()->cascadeOnDelete();
            $table->timestamp('added_at')->useCurrent();

            $table->primary(['collection_id', 'reel_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('collection_reels');
    }
};
