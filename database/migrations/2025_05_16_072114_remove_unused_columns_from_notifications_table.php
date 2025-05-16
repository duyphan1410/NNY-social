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
        Schema::table('notifications', function (Blueprint $table) {
            // Thay bằng tên các cột bạn thực sự muốn xóa
            $table->dropColumn(['reference_id', 'read_at', 'processed_at', 'viewed_at', 'view_duration']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Khôi phục lại nếu cần (kiểu dữ liệu phải đúng)
            $table->string('reference_id')->nullable();
            $table->string('read_at')->nullable();
            $table->string('processed_at')->nullable();
            $table->string('viewed_at')->nullable();
            $table->string('view_duration')->nullable();
        });
    }
};
