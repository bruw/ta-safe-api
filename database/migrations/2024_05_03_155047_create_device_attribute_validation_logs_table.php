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
        Schema::create('device_attribute_validation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('device_id')->constrained();
            $table->string('attribute_source');
            $table->string('attribute_label');
            $table->string('attribute_value');
            $table->string('invoice_attribute_label');
            $table->text('invoice_attribute_value');
            $table->unsignedInteger('similarity_ratio');
            $table->unsignedInteger('min_similarity_ratio');
            $table->boolean('validated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_attribute_validation_logs');
    }
};
