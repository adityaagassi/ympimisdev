<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtimes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('overtime_id')->unique();
            $table->date('overtime_date');
            $table->string('day_status');
            $table->string('remark')->nullable();;
            $table->string('shift');
            $table->string('division');
            $table->string('department');
            $table->string('section');
            $table->string('subsection');
            $table->string('group');
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
        Schema::dropIfExists('overtimes');
    }
}
