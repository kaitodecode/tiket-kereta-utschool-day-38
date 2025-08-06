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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("train_id")->references("id")->on("trains");
            $table->foreignUuid("route_id")->references("id")->on("routes");
            $table->dateTime("departure_time");
            $table->dateTime("arrival_time");
            $table->decimal("price");
            $table->integer("seat_available");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
