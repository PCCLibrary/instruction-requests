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
            $table->string('instructor_name');
            $table->string('display_name');
            $table->string('instructor_email');
            $table->string('instructor_phone');
            $table->string('instruction_type');
            $table->string('course_modality');
            // Adjusting the librarian reference to match the 'users' table id type
            $table->unsignedBigInteger('librarian')->nullable(); // Assuming it's nullable based on your earlier schema
            // Adjusting the class_location reference to match the 'campuses' table id type
            $table->unsignedInteger('class_location');
            $table->string('course_department');
            $table->string('course_number');
            $table->softDeletes();
            $table->timestamps();

            // Assuming the users and campuses tables are already migrated
            $table->foreign('librarian')->references('id')->on('users');
            $table->foreign('class_location')->references('id')->on('campuses');
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
