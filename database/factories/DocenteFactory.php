<?php

namespace Database\Factories;

use App\Models\Docente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition()
    {
        // Datos del docente
        $docenteData = [
            'dni' => Str::random(10),
            'nombre1' => $this->faker->firstName,
            'nombre2' => $this->faker->firstName,
            'apellidop' => $this->faker->lastName,
            'apellidom' => $this->faker->lastName,
            'contra' => bcrypt('password'), // Contraseña predeterminada
            'email' => $this->faker->email,
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'tipo' => $this->faker->randomElement(['NOMBRADO', 'CONTRATADO']),
            'image' => $this->faker->imageUrl(640, 480, 'people', true),
        ];

        // Crear el docente
        $docente = Docente::create($docenteData);

        // Crear un usuario asociado al docente y asignar un rol
        $usuario = User::create([
            'name' => $docente->nombre1,
            'apellido' => $docente->apellidop,
            'sexo' => $docente->sexo,
            'password' => bcrypt('password'), // Contraseña por defecto
            'status' => 'ACTIVO',
            'email' => $docente->email,
            'image' => $docente->image,
        ]);

        // Asignar el rol de docente
        $docenteRole = Role::findById(2);  // Asume que el rol docente tiene ID = 2
        $usuario->assignRole($docenteRole);

        // Retornar el docente creado
        return $docente;
    }
}

