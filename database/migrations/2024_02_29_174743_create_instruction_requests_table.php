<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instruction_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('instructor_id'); // Ensure this matches the `instructors.id` type
            $table->string('instruction_type'); // synchronous or asynchronous
            $table->string('course_modality');
            $table->unsignedBigInteger('librarian_id');
            $table->unsignedInteger('campus_id');
            $table->string('department');
            $table->string('course_number')->nullable();
            $table->unsignedInteger('class_id');
            $table->string('course_crn');
            $table->integer('number_of_students')->nullable();
            $table->boolean('ada_provisions_needed')->default(false);
            $table->text('ada_provisions_description')->nullable();
            $table->datetime('preferred_datetime');
            $table->datetime('alternate_datetime')->nullable();
            $table->string('duration')->nullable();
            $table->date('asynchronous_instruction_ready_date')->nullable();
            $table->text('extra_time_with_class')->nullable();
            // Attachments handled by Mediable
            $table->text('learning_outcomes')->nullable();
            $table->boolean('received_assignment')->default(false);
            $table->boolean('selected_topics')->default(false);
            $table->boolean('explored_background')->default(false);
            $table->boolean('written_draft')->default(false);
            $table->boolean('other_learning_outcome')->default(false);
            $table->text('other_learning_outcome_description')->nullable();
            $table->text('desired_student_outcomes')->nullable();
            $table->text('genai_discussion_interest')->nullable();
            $table->text('other_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->foreign('campus_id')->references('id')->on('campuses');
            $table->foreign('class_id')->references('id')->on('classes');
            $table->foreign('librarian_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instruction_requests');
    }
}
