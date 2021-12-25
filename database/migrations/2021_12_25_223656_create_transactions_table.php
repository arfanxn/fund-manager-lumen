<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("fund_id")->references("id")->on("funds")->onDelete("cascade");
            $table->unsignedBigInteger("amount")->default(0);
            $table->string("type", 10);
            $table->string("note", 1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
