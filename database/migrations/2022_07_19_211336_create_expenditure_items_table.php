<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenditureItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenditure_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->double('amount');
            $table->string('scan_picture')->nullable(true);//receipt
            $table->boolean('approve')->default(false);
            $table->text('comment')->nullable(true);
            $table->string('venue');
            $table->date('date');
            $table->timestamps();
            $table->uuid('expenditure_category_id');

            $table->foreign('expenditure_category_id')->references('id')->on('expenditure_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenditure_items');
    }
}
