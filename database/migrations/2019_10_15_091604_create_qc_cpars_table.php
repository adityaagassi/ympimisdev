<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQcCparsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_cpars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cpar_no');
            $table->string('kategori');
            $table->string('employee_id');
            $table->string('lokasi');
            $table->date('tgl_permintaan');
            $table->date('tgl_balas');
            $table->enum('via_komplain', ['Email','Telepon']);
            $table->string('file');
            $table->integer('department_id');
            $table->string('sumber_komplain');
            $table->string('tindakan');
            $table->string('penyebab');
            $table->enum('status_code', ['0','1']);
            $table->bigInteger('biaya');
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
        Schema::dropIfExists('qc_cpars');
    }
}
