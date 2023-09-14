<?php

use App\Enums\Device\DeviceValidationStatus;
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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_model_id');

            $table->enum('validation_status', array_column(
                DeviceValidationStatus::cases(),
                'name'
            ))->default('pending');

            $table->string('color');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('device_model_id')
                ->references('id')
                ->on('device_models');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
