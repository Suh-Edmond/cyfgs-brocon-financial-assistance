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
            $table->uuid('id')->primary();
            $table->string('name');
            $table->mediumText('description');
            $table->double('amount');
            $table->date('date');
            $table->boolean('approve')->default(false);
            $table->string('venue');
            $table->string('scan_picture')->nullable(true);
            $table->timestamps();
            $table->string('updated_by');
            $table->uuid('organisation_id');

            $table->foreign('organisation_id')->references('id')->on('organisations')->cascadeOnDelete();

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
