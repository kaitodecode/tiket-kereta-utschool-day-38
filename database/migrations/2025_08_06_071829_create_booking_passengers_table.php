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
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("booking_id")->references("id")->on("bookings");
            $table->string("name");
            $table->string("id_number");
            $table->integer("seat_number");
            $table->enum("status", ["child", "adult"]);
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
        Schema::dropIfExists('booking_passengers');
    }
};
