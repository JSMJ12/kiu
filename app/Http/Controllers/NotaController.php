<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Asignatura;
use App\Models\Alumno;
use App\Models\Matricula;

class NotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show($alumno_dni)
    {
        $alumno = Alumno::find($alumno_dni);
        $notas = Nota::where('alumno_dni', $alumno_dni)->with('asignatura')->get();

        return view('notas.index', compact('alumno', 'notas'));
    }

    public function create($alumno_dni)
    {
        $alumno = Alumno::findOrFail($alumno_dni);
        $matriculas = $alumno->matriculas;
        $asignaturas = collect();
        foreach ($matriculas as $matricula) {
            $cohorte = $matricula->cohorte;
            $asignaturas = $asignaturas->merge($cohorte->asignaturas);
        }

        return view('notas.create', compact('alumno', 'asignaturas'));
    }
    public function store(Request $request)
    {
        $alumno_dni = $request->input('alumno_dni');
        $notas_actividades = $request->input('nota_actividades');
        $notas_practicas = $request->input('nota_practicas');
        $notas_autonomo = $request->input('nota_autonomo');
        $examenes_finales = $request->input('examen_final');
        $recuperaciones = $request->input('recuperacion');
        $totales = $request->input('total');

        foreach ($notas_actividades as $asignatura_id => $nota_actividades) {
            $alumno = Alumno::findOrFail($alumno_dni);
            $asignatura = Asignatura::findOrFail($asignatura_id);
            $docente_dni = $asignatura->docentes->first()->dni;

            $matricula = Matricula::where('alumno_dni', $alumno_dni)
                ->where('asignatura_id', $asignatura_id)
                ->whereHas('cohorte', function($query) use ($docente_dni) {
                    $query->where('docente_dni', $docente_dni);
                })
                ->firstOrFail();

            $cohorte = $matricula->cohorte;

            // Usar updateOrCreate para actualizar o crear la nota
            Nota::updateOrCreate(
                [
                    'alumno_dni' => $alumno_dni,
                    'asignatura_id' => $asignatura_id,
                    'cohorte_id' => $cohorte->id,
                    'docente_dni' => $docente_dni,
                ],
                [
                    'nota_actividades' => $nota_actividades,
                    'nota_practicas' => $notas_practicas[$asignatura_id],
                    'nota_autonomo' => $notas_autonomo[$asignatura_id],
                    'examen_final' => $examenes_finales[$asignatura_id],
                    'recuperacion' => $recuperaciones[$asignatura_id],
                    'total' => $totales[$asignatura_id],
                ]
            );
        }

        return redirect()->route('notas.show', $alumno_dni)->with('success', 'Notas guardadas exitosamente');
    }

    public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();
        return redirect()->route('notas.index')
            ->with('success', 'Nota eliminada exitosamente.');
    }
}

