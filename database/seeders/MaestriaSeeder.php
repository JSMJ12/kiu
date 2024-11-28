<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maestria;
use App\Models\Docente;

class MaestriaSeeder extends Seeder
{
    public function run()
    {
        // Obtener los DNIs de los primeros 10 docentes (sin repetir)
        $docentes = Docente::inRandomOrder()->take(10)->pluck('dni');

        // Crear 10 maestrías con datos únicos para cada docente
        foreach ($docentes as $dni) {
            Maestria::create([
                'nombre' => 'Maestría en ' . fake()->word(),
                'coordinador' => $dni,
                'inscripcion' => fake()->numberBetween(500, 1000),
                'matricula' => fake()->numberBetween(1500, 3000),
                'arancel' => fake()->numberBetween(10000, 20000),
            ]);
        }
    }
}
