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
        Schema::create('directions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country', 60);
            $table->string('state', 60);
            $table->string('city', 60);
            $table->string('locality', 100);
            $table->string('street', 100);
            $table->integer('number');
            $table->string('department', 6)->nullable();
            $table->string('house', 6)->nullable();
            $table->string('floor', 4)->nullable();
            $table->integer('cp');
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
        Schema::dropIfExists('directions');
    }
};
