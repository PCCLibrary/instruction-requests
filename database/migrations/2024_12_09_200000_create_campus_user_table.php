<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        Log::info('Creating campus_user pivot table');

        Schema::create('campus_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('campus_id');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('campus_id')
                  ->references('id')
                  ->on('campuses')
                  ->onDelete('cascade');

            $table->unique(['user_id', 'campus_id']);
        });

        Log::info('Migrating existing campus_id data to pivot table');

        $migratedCount = 0;
        DB::table('users')
            ->whereNotNull('campus_id')
            ->each(function ($user) use (&$migratedCount) {
                DB::table('campus_user')->insert([
                    'user_id' => $user->id,
                    'campus_id' => $user->campus_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $migratedCount++;
            });

        Log::info("Migrated {$migratedCount} existing campus assignments to pivot table");
    }

    public function down(): void
    {
        Log::info('Dropping campus_user pivot table');
        Schema::dropIfExists('campus_user');
    }
};
