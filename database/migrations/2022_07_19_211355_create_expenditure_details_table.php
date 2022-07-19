<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenditureDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenditure_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('amount_given');
            $table->double('amount_spent');
            $table->string('name');
            $table->longText('comment');
            $table->string('scan_picture');
            $table->timestamps();
            $table->mediumText('created_by')->nullable(true);
            $table->mediumText('updated_by')->nullable(true);
            $table->string('expenditure_item_id');

            $table->foreign('expenditure_item_id')->references('id')->on('expenditure_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenditure_details');
    }
}
