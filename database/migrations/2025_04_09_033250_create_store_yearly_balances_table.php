<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreYearlyBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_yearly_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->double('balance');
            $table->string('updated_by');
            $table->timestamps();
        });

        \App\Models\StoreYearlyBalance::create([
            'session_id' => '8015d366-baf8-4a72-bec2-d473ca3ad0f8',
            'balance'     => 340400,
            'updated_by' => 'default'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_yearly_balances');
    }
}
