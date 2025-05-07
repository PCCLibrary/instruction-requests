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
        Schema::table('instruction_requests', function (Blueprint $table) {
            // Required by eloquent-lockable package
            $table->boolean('locked')->default(false);

            // Our extension to track who locked it and when
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at')->nullable();

            // Add foreign key constraint
            $table->foreign('locked_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instruction_requests', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['locked', 'locked_by', 'locked_at']);
        });
    }
};
