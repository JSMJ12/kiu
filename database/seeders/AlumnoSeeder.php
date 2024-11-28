<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AlumnoSeeder extends Seeder
{
    public function run()
    {
        // Crear 75 alumnos
        foreach (range(1, 5) as $index) {
            // Obtener la maestría aleatoria
            $maestria = Maestria::inRandomOrder()->first();
            $arancel = $maestria->arancel;

            // Obtener el próximo número de registro
            $nuevoRegistro = Alumno::where('maestria_id', $maestria->id)->count() + 1;

            // Generar un dni único
            do {
                $dni = 'DNI' . rand(100000000, 999999999); 
            } while (Alumno::where('dni', $dni)->exists()); // Verifica que el dni no exista ya

            // Generar un correo electrónico único
            do {
                $emailInstitucional = 'email' . $index . '@institucional.com';
            } while (User::where('email', $emailInstitucional)->exists()); // Verifica que el email no exista ya

            // Crear un nuevo objeto Alumno
            $alumno = new Alumno();
            $alumno->nombre1 = 'Nombre' . $index;
            $alumno->nombre2 = 'Nombre2' . $index;
            $alumno->apellidop = 'ApellidoP' . $index;
            $alumno->apellidom = 'ApellidoM' . $index;
            $alumno->contra = bcrypt('123456'); // Contraseña por defecto
            $alumno->sexo = $index % 2 == 0 ? 'M' : 'F';
            $alumno->dni = $dni; // Usar el dni único
            $alumno->email_institucional = $emailInstitucional; // Usar el email único
            $alumno->email_personal = 'email' . $index . '@personal.com';
            $alumno->estado_civil = 'Soltero';
            $alumno->fecha_nacimiento = Carbon::now()->subYears(rand(18, 35));
            $alumno->provincia = 'Provincia' . $index;
            $alumno->canton = 'Canton' . $index;
            $alumno->barrio = 'Barrio' . $index;
            $alumno->direccion = 'Direccion' . $index;
            $alumno->nacionalidad = 'Nacionalidad' . $index;
            $alumno->etnia = 'Etnia' . $index;
            $alumno->carnet_discapacidad = 'Carnet' . rand(1000, 9999);
            $alumno->tipo_discapacidad = 'Discapacidad' . rand(1, 5);
            $alumno->maestria_id = $maestria->id;
            $alumno->porcentaje_discapacidad = rand(0, 100);
            $alumno->registro = $nuevoRegistro;
            $alumno->monto_total = $arancel; // Asignar el valor del arancel

            // Generar una imagen por defecto (avatar)
            $primeraLetra = substr($alumno->nombre1, 0, 1);
            $alumno->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);

            $alumno->save();

            // Crear un nuevo objeto User
            $usuario = new User();
            $usuario->name = $alumno->nombre1;
            $usuario->apellido = $alumno->apellidop;
            $usuario->sexo = $alumno->sexo;
            $usuario->password = bcrypt('123456'); // Contraseña por defecto
            $usuario->status = 'ACTIVO';
            $usuario->email = $alumno->email_institucional;
            $usuario->image = $alumno->image;

            // Asignar rol de Alumno
            $alumnoRole = Role::findById(4); // Asegúrate de que el ID del rol de alumno sea 4
            $usuario->assignRole($alumnoRole);

            $usuario->save();
        }

        // Mensaje de éxito
        $this->command->info('Se crearon 5 alumnos con sus usuarios asociados.');
    }
}
