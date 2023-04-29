<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 10);
            $table->enum('approve', ['PENDING', 'APPROVED', 'DECLINED'])->default('PENDING');
            $table->uuid('user_id');
            $table->string('updated_by');
            $table->timestamps();

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
        Schema::dropIfExists('member_registrations');
    }
}
