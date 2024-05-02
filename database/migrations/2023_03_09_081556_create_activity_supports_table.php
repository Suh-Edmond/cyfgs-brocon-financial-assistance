<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_supports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('scan_picture')->nullable(true);
            $table->longText('comment')->nullable(true);
            $table->decimal('amount_deposited', 10, 2);
            $table->enum('approve', ['PENDING', 'APPROVED', 'DECLINED'])->default('PENDING');
            $table->string('supporter');
            $table->uuid('payment_item_id');
            $table->string('updated_by');
            $table->timestamps();

            $table->foreign('payment_item_id')->references('id')->on('payment_items')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_supports');
    }
}
