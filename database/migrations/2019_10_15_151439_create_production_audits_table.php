<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_audits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->integer('point_check_audit_id');
            $table->date('date');
            $table->string('foto_kondisi_aktual');
            $table->string('kondisi');
            $table->string('pic');
            $table->string('auditor');
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
        Schema::dropIfExists('production_audits');
    }
}
