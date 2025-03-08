<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reel_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_info')->nullable();
            $table->decimal('view_duration', 5, 2)->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('viewed_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reel_views');
    }
};
