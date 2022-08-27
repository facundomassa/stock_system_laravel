<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('origen_id_stockcenter');
            $table->unsignedInteger('destiny_id_stockcenter');
            $table->dateTime('date_ended')->nullable();
            $table->unsignedInteger('id_user');
            $table->string('status', 1)->default('I');
            $table->foreign('origen_id_stockcenter')->references('id')->on('stockcenters')->onDelete('cascade');
            $table->foreign('destiny_id_stockcenter')->references('id')->on('stockcenters')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('refers');
    }
};
