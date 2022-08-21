<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_contributions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('scan_picture')->nullable(true);
            $table->longText('comment')->nullable(true);
            $table->double('amount_deposited');
            $table->string('status');
            $table->boolean('approve')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_item_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_item_id')->references('id')->on('payment_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_contributions');
    }
}
