<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cocd');
            $table->string('porg');
            $table->string('group');
            $table->string('vendor')->unique();
            $table->text('name');
            $table->text('street')->nullable();
            $table->string('postl_code')->nullable();
            $table->text('city')->nullable();
            $table->string('cty');
            $table->string('language');
            $table->string('telephone_1')->nullable();
            $table->string('fax_number')->nullable();
            $table->string('sc')->nullable();
            $table->string('reconacct');
            $table->string('payt');
            $table->string('chk_dinv')->nullable();
            $table->string('pmnt_meths')->nullable();
            $table->string('tpay');
            $table->string('crcy');
            $table->string('incot')->nullable();
            $table->string('c')->nullable();
            $table->string('salesperson')->nullable();
            $table->string('gr-iv')->nullable();
            $table->string('arq')->nullable();
            $table->string('aut')->nullable();
            $table->string('funct');
            $table->string('createdby');
            $table->date('date');
            $table->string('delfpurorg')->nullable();
            $table->string('delf')->nullable();
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
        Schema::dropIfExists('vendors');
    }
}
