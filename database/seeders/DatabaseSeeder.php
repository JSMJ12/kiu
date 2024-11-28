<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Maestria;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class, 
            DocenteSeeder::class,
            MaestriaSeeder::class,
            AsignaturaSeeder::class,
            PeriodoAcademicoSeeder::class,
            AulaSeeder::class,
            CohorteSeeder::class,
        ]);
    }
}
