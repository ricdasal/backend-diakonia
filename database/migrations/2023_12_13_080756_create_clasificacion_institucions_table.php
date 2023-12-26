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
        Schema::create('clasificacion_institucion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clasificacion_id');
            $table->unsignedBigInteger('institucion_id');
            $table->foreign('institucion_id')->references('id')->on('institucion');
            $table->foreign('clasificacion_id')->references('id')->on('clasificacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificacion_institucion');
    }
};
