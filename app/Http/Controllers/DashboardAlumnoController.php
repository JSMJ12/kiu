<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Nota;
use App\Models\Docente;

class DashboardAlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();

        // Buscar el alumno basado en nombre1, apellidop, y email_institucional del usuario autenticado
        $alumno = Alumno::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('email_institucional', $user->email)
            ->firstOrFail();

        // Eager load de relaciones necesarias
        $alumno->load([
            'matriculas.asignatura.cohortes.aula',
            'matriculas.asignatura.notas' => function ($query) use ($alumno) {
                $query->where('alumno_dni', $alumno->dni);
            }
        ]);

        
        $asignaturas = $alumno->matriculas->map->asignatura;
        $notas = $asignaturas->mapWithKeys(function ($asignatura) use ($alumno) {
    
            $nota = $asignatura->notas->first();
            $cohorte = $asignatura->cohortes->first();
            $docente = $cohorte ? $cohorte->docentes->first() : null;

            return [
                $asignatura->nombre => [
                    'actividades_aprendizaje' => $nota->nota_actividades ?? 'N/A',
                    'practicas_aplicacion' => $nota->nota_practicas ?? 'N/A',
                    'aprendizaje_autonomo' => $nota->nota_autonomo ?? 'N/A',
                    'examen_final' => $nota->examen_final ?? 'N/A',
                    'recuperacion' => $nota->recuperacion ?? 'N/A',
                    'total' => $nota->total ?? 'N/A',
                    'aula' => $cohorte && $cohorte->aula ? $cohorte->aula->nombre : 'N/A',
                    'paralelo' => $cohorte && $cohorte->aula && $cohorte->aula->paralelo ? $cohorte->aula->paralelo->nombre : 'N/A',
                    'docente' => $docente ? $docente->nombre1 . ' ' . $docente->nombre2 . ' ' . $docente->apellidop . ' ' . $docente->apellidom : 'N/A',
                    'docente_image' => $docente ? $docente->image : 'default_image_path.jpg',
                ]
            ];
        });

        return view('dashboard.alumno', compact('asignaturas', 'notas', 'perPage', 'alumno'));
    }

}
