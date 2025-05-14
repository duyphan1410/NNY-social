<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('message');
            $table->string('url')->nullable();
            $table->string('type')->default('info');
            $table->json('data')->nullable();
            $table->string('reference_id')->nullable()->comment('ID tham chiếu đến đối tượng liên quan');
            $table->timestamp('read_at')->nullable()->comment('Thời điểm thông báo được đọc');
            $table->timestamp('processed_at')->nullable()->comment('Thời điểm thông báo được xử lý');
            $table->timestamp('viewed_at')->nullable()->comment('Thời điểm thông báo được xem chi tiết');
            $table->integer('view_duration')->nullable()->comment('Thời gian xem thông báo (giây)');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('read_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
