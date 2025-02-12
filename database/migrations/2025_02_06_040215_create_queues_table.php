<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();  // ✅ BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('employee_id');
            $table->integer('queue_number');
            $table->date('queue_date')->default(DB::raw('CURRENT_DATE')); // Resets daily
            $table->enum('status', ['waiting', 'in_consultation', 'completed'])->default('waiting');
            $table->timestamp('time_registered')->useCurrent();
            $table->timestamp('time_called')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('queues'); // ✅ Correct table name
    }
};

