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
        Schema::create('caracterizacion_institucion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caracterizacion_id');
            $table->unsignedBigInteger('institucion_id');
            $table->foreign('caracterizacion_id')->references('id')->on('caracterizacion');
            $table->foreign('institucion_id')->references('id')->on('institucion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caracterizacion_institucion');
    }
};
