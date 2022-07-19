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
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('scan_picture');
            $table->longText('comment');
            $table->double('amount_deposited');
            $table->enum('status', ['COMPLETE', 'INCOMPLETE']);
            $table->boolean('approve')->default(false);
            $table->timestamps();
            $table->mediumText('created_by')->nullable(true);
            $table->mediumText('updated_by')->nullable(true);
            $table->string('user_id');
            $table->string('payment_item_id');

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
