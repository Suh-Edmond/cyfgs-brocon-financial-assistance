<?php

use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_items', function (Blueprint $table){
           $table->enum('type', [PaymentItemType::ALL_MEMBERS, PaymentItemType::A_MEMBER,PaymentItemType::GROUPED_MEMBERS, PaymentItemType::MEMBERS_WITH_ROLES, PaymentItemType::MEMBERS_WITHOUT_ROLES]);
           $table->enum('frequency', [PaymentItemFrequency::YEARLY, PaymentItemFrequency::QUARTERLY, PaymentItemFrequency::ONE_TIME, PaymentItemFrequency::MONTHLY]);
           $table->longText('reference')->nullable(true);//a particular member or a list of members
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
