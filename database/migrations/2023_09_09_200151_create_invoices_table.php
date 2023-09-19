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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('access_key', 44)->unique();
            $table->string('consumer_cpf', 14)->nullable();
            $table->string('consumer_name')->nullable();
            $table->text('product_description')->nullable();

            $table->unsignedBigInteger('device_id');
            $table->foreign('device_id')
                ->references('id')
                ->on('devices');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
