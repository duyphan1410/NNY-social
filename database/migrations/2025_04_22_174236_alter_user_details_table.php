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
        Schema::table('user_details', function (Blueprint $table) {
            // Đổi tên cột 'cover' thành 'cover_img_url'
            $table->renameColumn('cover', 'cover_img_url');

            // Thay đổi kiểu dữ liệu và độ dài của cột 'bio' (nếu cần)
            $table->string('bio', 500)->nullable()->change(); // Tăng độ dài lên 500

            // Thêm các cột mới
            $table->string('website')->nullable();
            $table->string('relationship_status')->nullable();
            $table->text('hobbies')->nullable();
            $table->json('social_links')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Xóa các cột đã thêm
            $table->dropColumn('website');
            $table->dropColumn('relationship_status');
            $table->dropColumn('hobbies');
            $table->dropColumn('social_links');

            // Đổi tên cột 'cover_img_url' trở lại 'cover'
            $table->renameColumn('cover_img_url', 'cover');

            // Thay đổi kiểu dữ liệu và độ dài của cột 'bio' trở lại (nếu cần)
            $table->string('bio', 255)->nullable()->change();
        });
    }
};
