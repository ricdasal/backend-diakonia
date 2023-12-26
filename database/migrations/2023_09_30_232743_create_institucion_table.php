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
        Schema::create('institucion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',200);
            $table->string('representante_legal',250);
            $table->string('ruc',200);
            $table->integer('numero_beneficiarios');//->nullable();
            //$table->foreignId('red_bda_id')->constrained('red_bda');
            //$table->foreignId('poblacion_id')->constrained('tipo_poblacion');
            //$table->foreignId('clasificacion_id')->constrained('clasificacion');
            //$table->foreignId('estado_id')->constrained('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institucion');
    }
};
