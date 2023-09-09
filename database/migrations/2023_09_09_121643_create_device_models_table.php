<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('chipset');
            $table->string('ram');
            $table->string('storage');
            $table->string('screen_size');
            $table->string('screen_resolution');
            $table->string('battery_capacity');
            $table->string('year_of_manufacture');
            $table->string('os');
            $table->timestamps();

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_models');
    }
};
