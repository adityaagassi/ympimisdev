<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material_number');
            $table->string('due_date');
            $table->double('quantity');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['material_number', 'due_date'], 'production_schedule_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_schedules');
    }
}
