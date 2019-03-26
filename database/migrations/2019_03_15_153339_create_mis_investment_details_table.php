<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMisInvestmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mis_investment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project');
            $table->string('item_code');
            $table->string('description');
            $table->string('uom');
            $table->double('qty');
            $table->double('price');
            $table->string('type');
            $table->string('category');
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('mis_investment_details');
    }
}
