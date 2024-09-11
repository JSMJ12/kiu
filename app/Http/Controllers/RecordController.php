<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Secretario;
use App\Models\Docente;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;



class RecordController extends Controller
{
    public function show($alumno_dni)
    {
        // Obtener el alumno y sus notas
        $alumno = Alumno::findOrFail($alumno_dni);
        $notas = $alumno->notas()->with('asignatura', 'docente')->get();

        $seccionId = $alumno->maestria->secciones->first()->id;

        $secretarios = Secretario::where('seccion_id', $seccionId)->get();

        $totalCreditos = $notas->sum(function ($nota) {
            return $nota->asignatura->credito;
        });

        // Obtener la cohorte del alumno
        $cohorte = $alumno->maestria->cohorte->first();

        preg_match('/Cohorte (\w+)/', $cohorte->nombre, $matches);
        $numeroRomano = $matches[1] ?? '';

        // Acceder a los datos de periodo_academico en la cohorte
        $periodo_academico = $cohorte->periodo_academico;

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');
        // Generar el código QR con el enlace al PDF
        $pdfPath = 'pdfs/' . $alumno->apellidop . $alumno->nombre1 . '_notas.pdf';
        $url = url($pdfPath);

        // Reemplazar el esquema "https" con "http"
        $httpUrl = str_replace('https://', 'http://', $url);

        $logoPath = public_path('images/posgrado_logo_2.png');

        // Generar el código QR con logotipo
        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->merge($logoPath, 0.3, true)
            ->generate($httpUrl);

            $coordinadorDni = $alumno->maestria->coordinador;

            // Buscar al docente utilizando el DNI
            $coordinador = Docente::where('dni', $coordinadorDni)->first();
            
            if ($coordinador) {
                // Acceder al nombre completo utilizando el método getFullNameAttribute
                $nombreCompleto = $coordinador->getFullNameAttribute();
            } else {
                $nombreCompleto = 'Coordinador no encontrado';
            }
        // Crear una instancia de Dompdf con las opciones
        $pdf = Pdf::loadView('record.show', compact('secretarios', 'alumno', 'notas', 'periodo_academico', 'cohorte', 'totalCreditos', 'numeroRomano', 'fechaActual', 'qrCode', 'nombreCompleto'));

        // Directorio para almacenar los PDFs
        $pdfDirectory = public_path('pdfs');

        // Verificar si el directorio existe, si no, crearlo
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }
        // Guardar el PDF
        $pdf->save(public_path($pdfPath));

        return $pdf->stream($alumno->apellidop . $alumno->nombre1 . '_notas.pdf', ['Attachment' => 0]);
    }

}
