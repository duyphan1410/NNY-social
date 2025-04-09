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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // liên kết với bảng users
            $table->string('cover')->nullable();         // ảnh bìa
            $table->string('bio', 255)->nullable();      // mô tả ngắn
            $table->string('location')->nullable();      // địa chỉ
            $table->date('birthday')->nullable();        // ngày sinh
            $table->enum('gender', ['male', 'female', 'other'])->nullable(); //giới tính
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
