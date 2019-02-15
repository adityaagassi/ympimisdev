<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_checksheet');
            $table->integer('inspection1')->default('0');
            $table->integer('inspection2')->default('0');
            $table->integer('inspection3')->default('0');
            $table->integer('inspection4')->default('0');
            $table->integer('inspection5')->default('0');
            $table->integer('inspection6')->default('0');
            $table->integer('inspection7')->default('0');
            $table->integer('inspection8')->default('0');
            $table->integer('inspection9')->default('0');
            $table->string('remark1');
            $table->string('remark2');
            $table->string('remark3');
            $table->string('remark4');
            $table->string('remark5');
            $table->string('remark6');
            $table->string('remark7');
            $table->string('remark8');
            $table->string('remark9');
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
        Schema::dropIfExists('inspections');
    }
}
