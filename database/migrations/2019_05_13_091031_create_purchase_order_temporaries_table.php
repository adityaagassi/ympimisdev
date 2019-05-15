<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderTemporariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_temporaries', function (Blueprint $table) {
            $table->string('purchdoc');
            $table->string('item');
            $table->date('deliv_date');
            $table->double('order_qty');
            $table->unique(['purchdoc', 'item', 'deliv_date'], 'purchase_order_temporary_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('breaks');
    }
}
