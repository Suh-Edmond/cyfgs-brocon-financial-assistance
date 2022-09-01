<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique()->nullable(true);
            $table->string('telephone')->unique(true);
            $table->timestamp('email_verified_at')->nullable(true);
            $table->string('password')->nullable(true);
            $table->string('gender')->nullable(true);
            $table->string('address')->nullable(true);
            $table->string('occupation')->nullable(true);
            $table->rememberToken();
            $table->timestamps();
            $table->uuid('organisation_id')->nullable(true);

            $table->foreign('organisation_id')->references('id')->on('organisations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
