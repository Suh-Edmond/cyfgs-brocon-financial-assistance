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
            $table->longText('comment')->nullable(true);
            $table->enum('approve', ['PENDING', 'APPROVED', 'DECLINED'])->default('PENDING');
            $table->string('scan_picture')->nullable(true);
            $table->timestamps();
            $table->uuid('expenditure_item_id');
            $table->string('updated_by');

            $table->foreign('expenditure_item_id')->references('id')->on('expenditure_items')->cascadeOnDelete();
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
