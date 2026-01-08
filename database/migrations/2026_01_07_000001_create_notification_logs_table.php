<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('instruction_request_id');
            $table->string('notification_type', 50);
            $table->string('recipient_type', 50);
            $table->unsignedBigInteger('recipient_id');
            $table->string('recipient_email', 255);
            $table->timestamp('sent_at');
            $table->unsignedBigInteger('sent_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('instruction_request_id')
                ->references('id')
                ->on('instruction_requests')
                ->onDelete('cascade');

            $table->index('instruction_request_id', 'idx_request');
            $table->index('sent_at', 'idx_sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
