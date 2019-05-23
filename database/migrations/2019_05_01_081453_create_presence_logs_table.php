<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresenceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presence_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id');
            $table->date('presence_date');
            $table->time('in_time');
            $table->time('out_time');
            $table->string('shift');
            $table->string('remark')->nullable();
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['employee_id', 'presence_date'], 'presence_log_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presence_logs');
    }
}
