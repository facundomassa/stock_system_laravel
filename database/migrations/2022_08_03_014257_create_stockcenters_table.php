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
        Schema::create('stockcenters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_enterprise');
            $table->string('name', 60);
            $table->string('type', 1);
            $table->unsignedInteger('id_direction');
            $table->unsignedInteger('id_person')->nullable();
            $table->foreign('id_person')->references('id')->on('persons')->onDelete('cascade');
            $table->foreign('id_direction')->references('id')->on('directions')->onDelete('cascade');
            $table->foreign('id_enterprise')->references('id')->on('enterprises')->onDelete('cascade');
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
        Schema::dropIfExists('stockcenters');
    }
};
