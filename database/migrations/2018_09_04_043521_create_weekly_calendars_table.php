<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_calendars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fiscal_year');
            $table->string('week_name');
            $table->string('week_date');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['fiscal_year', 'week_name', 'week_date'], 'weekly_calendar_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weekly_calendars');
    }
}
