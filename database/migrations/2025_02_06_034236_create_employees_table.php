<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('name')->nullable();
            $table->string('race')->nullable();
            $table->string('nric_number');
            $table->string('passport_number');
            $table->string('nationatility')->nullable();
            $table->string('base')->nullable();
            $table->string('department')->nullable();
            $table->string('company')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};

