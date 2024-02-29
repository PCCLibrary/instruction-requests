<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instruction_request_details', function (Blueprint $table) {
            $table->increments('id'); // Primary key for the details table
            $table->unsignedInteger('instruction_request_id');
            $table->unsignedBigInteger('librarian_id')->nullable(); // Librarian might not be assigned initially
            $table->json('tasks_completed')->nullable();
            $table->string('status')->default('pending');
            $table->string('instruction_duration')->nullable();
            $table->text('class_notes')->nullable();
            $table->json('materials')->nullable();
            $table->text('assessment_notes')->nullable();
            $table->json('assessments')->nullable();
            $table->string('created_by');
            $table->string('last_updated_by');
            $table->softDeletes();
            $table->timestamps();

            // Define the foreign key constraint for instruction_request_id
            $table->foreign('instruction_request_id')->references('id')->on('instruction_requests')->onDelete('cascade');

            // Define the foreign key constraint for librarian_id
            $table->foreign('librarian_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instruction_request_details', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['instruction_request_id']);
            $table->dropForeign(['librarian_id']);
        });

        Schema::dropIfExists('instruction_request_details');
    }
}
