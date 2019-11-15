<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditReportActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_report_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('department');
            $table->string('section');
            $table->string('subsection');
            $table->date('date');
            $table->string('nama_dokumen');
            $table->string('no_dokumen');
            $table->string('kesesuaian_aktual_proses');
            $table->string('tindakan_perbaikan');
            $table->date('target');
            $table->string('kelengkapan_point_safety');
            $table->string('kesesuaian_qc_kouteihyo');
            $table->string('operator');
            $table->string('leader');
            $table->string('foreman');
            $table->string('send_status');
            $table->date('send_date');
            $table->string('oprator_sign');
            $table->string('approval');
            $table->date('approval_date');
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
        Schema::dropIfExists('audit_report_activities');
    }
}
