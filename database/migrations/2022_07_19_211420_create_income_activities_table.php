<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_activities', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->double('amount');
            $table->date('date');
            $table->boolean('approve')->default(false);
            $table->string('venue');
            $table->timestamps();
            $table->mediumText('created_by')->nullable(true);
            $table->mediumText('updated_by')->nullable(true);

            $table->unsignedBigInteger('organisation_id');

            $table->foreign('organisation_id')->references('id')->on('organisations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_activities');
    }
}
