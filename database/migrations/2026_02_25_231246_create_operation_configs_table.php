<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('operation_id');
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');

            $table->unsignedInteger('request_origin_id')->nullable();
            $table->foreign('request_origin_id')->references('id')->on('stockcenters')->onDelete('set null');

            $table->unsignedInteger('consume_destiny_id')->nullable();
            $table->foreign('consume_destiny_id')->references('id')->on('stockcenters')->onDelete('set null');
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
        Schema::dropIfExists('operation_configs');
    }
};
