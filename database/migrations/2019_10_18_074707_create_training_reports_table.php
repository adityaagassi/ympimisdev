<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('department');
            $table->string('section');
            $table->string('product');
            $table->string('periode');
            $table->date('date');
            $table->time('time');
            $table->string('trainer');
            $table->string('theme');
            $table->string('isi_training');
            $table->string('tujuan');
            $table->string('standard');
            $table->string('leader');
            $table->string('foreman');
            $table->string('notes');
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
        Schema::dropIfExists('training_reports');
    }
}
