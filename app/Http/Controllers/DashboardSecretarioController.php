<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Secretario;
use App\Models\Alumno;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\User;
use App\Models\Postulante;

class DashboardSecretarioController extends Controller
{   public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
        $maestrias = $secretario->seccion->maestrias;
        
        $postulantesPorMaestria = [];

        foreach ($maestrias as $maestria) {
            $cantidadPostulantes = Postulante::where('maestria_id', $maestria->id)->count();
            $postulantesPorMaestria[] = [
                'maestria' => $maestria->nombre,
                'cantidad_postulantes' => $cantidadPostulantes,
            ];
        }

        $perPage = $request->input('perPage', 10);
        $user = auth()->user();
        $alumnos = Alumno::with('maestria')->get();
        // Obtener datos para el gráfico de matriculados por maestría
        $matriculadosPorMaestria = Maestria::withCount('alumnos')->get();
        $totalMaestrias = Maestria::count();
        $totalDocentes = Docente::count();
        $totalSecretarios = Secretario::count();
        $totalUsuarios = User::count();
        $totalPostulantes = Postulante::count();
        $totalAlumnos = Alumno::count();
        return view('dashboard.administrador', 
        compact('alumnos', 'matriculadosPorMaestria', 'totalAlumnos', 
        'perPage', 'totalUsuarios', 'totalMaestrias', 'totalSecretarios', 'totalDocentes',
        'totalPostulantes', 'postulantesPorMaestria'));
    }
}
