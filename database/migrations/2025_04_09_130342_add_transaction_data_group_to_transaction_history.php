<?php

use App\Constants\TransactionDataGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionDataGroupToTransactionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('transaction_histories', function (Blueprint $table) {
//            $table->enum('transaction_data_group', [TransactionDataGroup::EXPENDITURE_ITEM_DETAILS, TransactionDataGroup::EXPENDITURE_ITEMS, TransactionDataGroup::INCOME_ACTIVITY, TransactionDataGroup::SPONSORSHIP,
//                TransactionDataGroup::USER_CONTRIBUTIONS, TransactionDataGroup::USER_REGISTRATION, TransactionDataGroup::USER_SAVING]);
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_history', function (Blueprint $table) {
            //
        });
    }
}
