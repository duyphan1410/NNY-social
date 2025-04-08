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
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('shared_post_id')->nullable()->after('user_id');
            $table->foreign('shared_post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['shared_post_id']);
            $table->dropColumn('shared_post_id');
        });
    }
};
