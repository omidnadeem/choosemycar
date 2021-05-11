<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 36)->unique();
            $table->char('unique_id',10)->unique();
            $table->unsignedInteger('vehicle_dealer_id');
            $table->foreign('vehicle_dealer_id')
                ->references('id')
                ->on('dealers')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('mark', 255)->nullable();
            $table->string('colour', 50)->nullable();
            $table->enum('fuel', ['Electric', 'Petrol', 'Diesel'])->nullable();
            $table->enum('status', ['Active', 'Processing', 'Sold'])->nullable();
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
        Schema::dropIfExists('vehicles');
    }
}
