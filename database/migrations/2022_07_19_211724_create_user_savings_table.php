<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_savings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('amount_deposited');
            $table->text('comment');
            $table->string('user_id');
            $table->boolean('approve')->default(false);
            $table->timestamps();
            $table->mediumText('created_by')->nullable(true);
            $table->mediumText('updated_by')->nullable(true);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_savings');
    }
}
