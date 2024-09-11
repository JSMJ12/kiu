<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoordinadorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();
        $alumnos = Alumno::with('maestria')->get();
        // Obtener datos para el gráfico de matriculados por maestría
        $matriculadosPorMaestria = Maestria::withCount('alumnos')->get();
        $totalMaestrias = Maestria::count();
        $totalDocentes = Docente::count();
        $totalSecretarios = Secretario::count();
        $totalUsuarios = User::count();
        $totalAlumnos = Alumno::count();
        $totalPostulantes = Postulante::count();

        return view('dashboard.secretario', 
        compact('alumnos', 'matriculadosPorMaestria', 'totalAlumnos', 
        'perPage', 'totalUsuarios', 'totalMaestrias', 'totalSecretarios', 'totalDocentes',
        'totalPostulantes'));
    }
}
