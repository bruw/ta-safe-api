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
            $table->string('color');
            $table->string('imei1', 15)->unique();
            $table->string('imei2', 15)->unique();

            $table->enum('validation_status', array_column(
                DeviceValidationStatus::cases(),
                'value'
            ))->default('pending');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('device_model_id');
            $table->foreign('device_model_id')
                ->references('id')
                ->on('device_models');

            $table->timestamps();
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
