<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Titulacion;
use App\Models\Alumno;
use Illuminate\Support\Facades\Storage;

class TitulacionController extends Controller
{
    public function store(Request $request, $alumnoDNI)
    {
        $request->validate([
            'titulado' => 'required|boolean',
            'tesis' => 'nullable|mimes:pdf|max:4048', // Ajusta los tipos y tamaños de archivo según tus necesidades
        ]);

        $alumno = Alumno::where('dni', $alumnoDNI)->firstOrFail();

        $titulacion = Titulacion::updateOrCreate(
            ['alumno_dni' => $alumno->dni],
            [
                'titulado' => $request->input('titulado'),
                'tesis_path' => $request->hasFile('tesis') ? $request->file('tesis')->store('tesis', 'public') : null,
            ]
        );

        return redirect()->route('alumnos.index')->with('success', 'Titulación registrada o actualizada exitosamente.');
    }
}
