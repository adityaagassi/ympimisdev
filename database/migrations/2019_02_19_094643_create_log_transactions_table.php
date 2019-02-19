<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material_number');
            $table->string('issue_plant');
            $table->string('issue_storage_location');
            $table->string('receive_plant');
            $table->string('receive_storage_location');
            $table->string('cost_center')->nullable();
            $table->string('gl_account')->nullable();
            $table->string('transaction_code');
            $table->string('mvt');
            $table->string('reason_code')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('transaction_date');
            $table->double('qty');
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
        Schema::dropIfExists('log_transactions');
    }
}
