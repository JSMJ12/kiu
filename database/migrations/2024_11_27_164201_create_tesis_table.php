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
        Schema::create('tesis', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_dni'); // Cambia a string para referenciar el DNI
            $table->foreign('alumno_dni')->references('dni')->on('alumnos')->onDelete('cascade');
            $table->string('tutor_id')->nullable(); // Cambia a string para referenciar el DNI
            $table->foreign('tutor_id')->references('dni')->on('docentes')->onDelete('set null');
            $table->string('tema');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis');
    }
};