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
            $table->string('scan_picture')->nullable(true);
            $table->longText('comment')->nullable(true);
            $table->double('amount_deposited');
            $table->string('status');
            $table->enum('approve', ['PENDING', 'APPROVED', 'DECLINED'])->default('PENDING');
            $table->timestamps();
            $table->uuid('user_id');
            $table->uuid('payment_item_id');
            $table->string('updated_by');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->cascadeOnDelete();
            $table->foreign('payment_item_id')->references('id')->on('payment_items')->onDelete('cascade')->cascadeOnDelete();
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
