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
           $table->enum('type', ['ALL_MEMBERS', 'MEMBER_WITH_ROLES', 'MEMBERS_WITHOUT_ROLES', 'A_MEMBER']);
           $table->enum('frequency', ['YEARLY', 'ONE_TIME', 'MONTHLY', 'QUARTERLY']);
           $table->uuid('user_id')->nullable(true);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

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
