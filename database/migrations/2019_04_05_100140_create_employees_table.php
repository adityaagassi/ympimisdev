<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id');
            $table->string('name');
            $table->string('gender');
            $table->string('family_id');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('address');
            $table->string('phone');
            $table->string('wa_number');
            $table->string('card_id');
            $table->string('account');
            $table->string('bpjstk');
            $table->string('jp');
            $table->string('bpjskes');
            $table->string('npwp');
            $table->string('direct_superior');
            $table->date('hire_date');
            $table->date('end_date');
            $table->string('avatar');
            $table->string('remark')
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
        Schema::dropIfExists('employees');
    }
}