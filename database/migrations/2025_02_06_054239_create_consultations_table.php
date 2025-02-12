<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->unsignedBigInteger('doctor_id');  // ✅ Must be UNSIGNED
            $table->unsignedBigInteger('queue_id');   // ✅ Must be UNSIGNED
            $table->unsignedBigInteger('diagnosis_id')->nullable();
            $table->json('prescribed_medicines')->nullable();
            $table->timestamp('time_in')->useCurrent();
            $table->timestamp('time_out')->nullable();
            $table->boolean('granted_mc')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // ✅ Correct Foreign Keys
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('queue_id')->references('id')->on('queues')->onDelete('cascade'); // ✅ Corrected Table Name
            $table->foreign('diagnosis_id')->references('id')->on('diagnoses')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultations');
    }
};



