<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchdoc');
            $table->string('order_no');
            $table->date('order_date'); //tanggal buat PO
            $table->string('pgr')->nullable();
            $table->string('pgr_name')->nullable();
            $table->integer('rev_no'))->nullable(); //nomor revisi ke berapa
            $table->date('rev_date'))->nullable(); //tanggal buat revisi
            $table->string('vendor')->nullable();
            $table->string('name')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('postl_code')->nullable();
            $table->string('cty')->nullable();
            $table->string('salesperson')->nullable(); //confirmed to
            $table->string('sc')->nullable(); //ditentukan/default
            $table->string('sc_name')->nullable(); //ditentukan/default transportation
            $table->string('tpay')->nullable();
            $table->string('tpay_name')->nullable(); //payment term
            $table->string('telephone')->nullable();
            $table->string('fax_number')->nullable();
            $table->string('incot')->nullable(); //deliverty terms
            $table->string('curr')->nullable();
            $table->string('item')->nullable();
            $table->string('material')->nullable();
            $table->string('description')->nullable();
            $table->date('deliv_date')->nullable();
            $table->double('order_qty')->nullable();
            $table->string('base_unit_of_measure')->nullable();
            $table->double('price')->nullable();
            $table->double('amount')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
}
