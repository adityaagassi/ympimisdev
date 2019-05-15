<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('porg')->nullable();
            $table->string('pgr')->nullable();
            $table->string('vendor');
            $table->text('name');
            $table->string('country');
            $table->string('material');
            $table->string('description');
            $table->string('plnt');
            $table->string('sloc');
            $table->string('sc_vendor')->nullable();
            $table->string('cost_ctr')->nullable();
            $table->string('purchdoc');
            $table->string('item');
            $table->string('acctassigcat')->nullable();
            $table->date('order_date');
            $table->date('deliv_date');
            $table->double('order_qty');
            $table->double('deliv_qty');
            $table->string('base_unit_of_measure');
            $table->double('price');
            $table->string('curr');
            $table->string('order_no')->nullable();
            $table->date('reply_date')->nullable();
            $table->date('create_date')->nullable();
            $table->string('delay')->nullable();
            $table->double('reply_qty')->nullable();
            $table->string('comment')->nullable();
            $table->string('del')->nullable();
            $table->string('incomplete')->nullable();
            $table->string('compl')->nullable();
            $table->string('ctr')->nullable();
            $table->string('spt')->nullable();
            $table->double('stock')->nullable();
            $table->string('lt')->nullable();
            $table->string('dsf')->nullable();
            $table->string('die_end')->nullable();
            $table->integer('remark');
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
        Schema::dropIfExists('po_lists');
    }
}
