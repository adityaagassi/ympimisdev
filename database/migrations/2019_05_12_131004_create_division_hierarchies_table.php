<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivisionHierarchiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('division_hierarchies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parent');
            $table->string('child');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['parent', 'child'], 'division_hierarchy_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('division_hierarchies');
    }
}
