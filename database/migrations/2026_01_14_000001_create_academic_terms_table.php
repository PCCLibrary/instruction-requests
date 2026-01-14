<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year', 10);
            $table->string('term_name', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('sort_order');
            $table->timestamps();

            $table->unique(['academic_year', 'term_name'], 'unique_year_term');

            $table->index('academic_year');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
    }
};
