<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvertimeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtime_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('overtime_id');
            $table->string('employee_id');
            $table->string('cost_center');
            $table->boolean('food');
            $table->boolean('ext_food');
            $table->string('transport');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('purpose');
            $table->text('remark')->nullable();
            $table->double('final_hour');
            $table->double('final_overtime');
            $table->string('status');
            $table->string('ot_status');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overtime_details');
    }
}
