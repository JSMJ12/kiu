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
        Schema::create('titulaciones', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_dni'); 
            $table->boolean('titulado')->default(false); 
            $table->string('tesis_path')->nullable(); 
            $table->date('fecha_graduacion')->nullable(); 
            $table->timestamps();
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulaciones');
    }
};
