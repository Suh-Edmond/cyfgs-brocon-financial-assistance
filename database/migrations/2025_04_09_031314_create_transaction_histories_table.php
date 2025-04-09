<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('old_amount_deposited', 10);
            $table->decimal('new_amount_deposited', 10);
            $table->longText('reason')->nullable();
            $table->uuid('reference_data')->unique();
            $table->enum('approve', ['PENDING', 'APPROVED', 'DECLINED'])->default('PENDING');
            $table->string('code')->unique();
            $table->timestamps();
            $table->string('updated_by');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_histories');
    }
}
