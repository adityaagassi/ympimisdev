<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaizenFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kaizen_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id');
            $table->string('employee_name');
            $table->string('propose_date');
            $table->string('section');
            $table->string('sub_leader');
            $table->string('title');
            $table->string('condition');
            $table->string('improvement');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kaizen_forms');
    }
}
