<?php

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
           $table->enum('type', ['ALL_MEMBERS', 'MEMBER_WITH_ROLES', 'MEMBERS_WITHOUT_ROLES', 'A_MEMBER', 'GROUPED_MEMBERS']);
           $table->enum('frequency', ['YEARLY', 'ONE_TIME', 'MONTHLY', 'QUARTERLY']);
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
