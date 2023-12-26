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
        Schema::create('red_bda', function (Blueprint $table) {
            $table->id();
            $table->string('mes_ingreso',40);
            $table->integer('anio_ingreso');//->nullable();
            $table->foreignId('institucion_id')->constrained('institucion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('red_bda');
    }
};
