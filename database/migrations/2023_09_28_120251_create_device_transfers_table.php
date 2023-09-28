<?php

use App\Enums\Device\DeviceTransferStatus;
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
        Schema::create('device_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_user_id');
            $table->unsignedBigInteger('target_user_id');
            $table->unsignedBigInteger('device_id');

            $table->enum('status', array_column(
                DeviceTransferStatus::cases(),
                'value'
            ))->default('pending');

            $table->timestamps();

            $table->foreign('source_user_id')
                ->references('id')
                ->on('users');

            $table->foreign('target_user_id')
                ->references('id')
                ->on('users');

            $table->foreign('device_id')
                ->references('id')
                ->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_transfers');
    }
};
