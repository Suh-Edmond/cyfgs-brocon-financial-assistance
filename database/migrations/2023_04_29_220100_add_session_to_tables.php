<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_items', function (Blueprint $table) {
           $table->uuid('session_id');
           $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('expenditure_items', function (Blueprint $table) {
            $table->uuid('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('income_activities', function (Blueprint $table) {
            $table->uuid('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('member_registrations', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->uuid('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('user_savings', function (Blueprint $table) {
            $table->uuid('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('activity_supports', function (Blueprint $table) {
            $table->uuid('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('user_contributions', function (Blueprint $table) {
            $table->uuid('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
}
