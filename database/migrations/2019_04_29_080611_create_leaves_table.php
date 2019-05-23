<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id');
            $table->integer('leave_quota');
            $table->integer('leave_left');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('remark')->nullable();
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['employee_id', 'valid_from'], 'leave_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaves');
    }
}