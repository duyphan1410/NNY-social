<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->decimal('duration', 5, 2)->nullable();
            $table->string('file_url');
            $table->boolean('is_original')->default(false);
            $table->foreignId('original_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('use_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audios');
    }
};
