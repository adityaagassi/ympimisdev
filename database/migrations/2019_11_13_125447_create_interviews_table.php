<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('department');
            $table->string('section');
            $table->string('subsection');
            $table->string('periode');
            $table->date('date');
            $table->string('leader');
            $table->string('foreman');
            $table->string('send_status');
            $table->date('send_date');
            $table->string('approval');
            $table->date('approved_date');
            $table->string('created_by');
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
        Schema::dropIfExists('interviews');
    }
}
