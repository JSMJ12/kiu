<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postulante;
use App\Models\User;
use App\Models\Maestria;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Postulacion2;
use App\Events\SubirArchivoEvent;
use App\Notifications\SubirArchivoNotification;
class DashboardPostulanteController extends Controller
{
    
    public function index()
    {
        $user = auth()->user();
        $postulante = Postulante::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('correo_electronico', $user->email)
            ->with(['maestria.cohorte' => function ($query) {
                $query->latest();
            }])
            ->firstOrFail();
        if (
            is_null($postulante->pdf_cedula) || 
            is_null($postulante->pdf_papelvotacion) || 
            is_null($postulante->pdf_titulouniversidad) || 
            is_null($postulante->pdf_hojavida) || 
            ($postulante->discapacidad == "Si" && is_null($postulante->pdf_conadis))
        ) {
            $usuario = User::where('name', $postulante->nombre1)
                ->where('apellido', $postulante->apellidop)
                ->where('email', $postulante->correo_electronico)
                ->first();

            if ($usuario) {
                $usuario->notify(new SubirArchivoNotification($postulante));
            }
        }
        return view('dashboard.postulante', compact('postulante'));
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $postulante = Postulante::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('correo_electronico', $user->email)
            ->firstOrFail();

        Storage::makeDirectory('public/postulantes/pdf');

        // Inicializar variables para almacenar las rutas de los archivos
        $pdfCedulaPath = $postulante->pdf_cedula;
        $pdfPapelVotacionPath = $postulante->pdf_papelvotacion;
        $pdfTituloUniversidadPath = $postulante->pdf_titulouniversidad;
        $pdfHojavidaPath = $postulante->pdf_hojavida;
        $pdfConadisPath = $postulante->pdf_conadis;
        $pdfPagoMatriculaPath = $postulante->pago_matricula;

        if ($request->hasFile('pdf_cedula')) {
            $pdfCedulaPath = $request->file('pdf_cedula')->store('postulantes/pdf', 'public');
        }
        if ($request->hasFile('pdf_papelvotacion')) {
            $pdfPapelVotacionPath = $request->file('pdf_papelvotacion')->store('postulantes/pdf', 'public');
        }
        if ($request->hasFile('pdf_titulouniversidad')) {
            $pdfTituloUniversidadPath = $request->file('pdf_titulouniversidad')->store('postulantes/pdf', 'public');
        }
        if ($request->hasFile('pdf_hojavida')) {
            $pdfHojavidaPath = $request->file('pdf_hojavida')->store('postulantes/pdf', 'public');
        }
        if ($request->hasFile('pdf_conadis')) {
            $pdfConadisPath = $request->file('pdf_conadis')->store('postulantes/pdf', 'public');
        }
        if ($request->hasFile('pago_matricula')) {
            $pdfPagoMatriculaPath = $request->file('pago_matricula')->store('postulantes/pdf', 'public');
        }
        if ($request->hasFile('carta_aceptacion')) {
            $pdfCartaAceptacionPath = $request->file('carta_aceptacion')->store('postulantes/pdf', 'public');
        }

        $updateData = [];
        if (isset($pdfCedulaPath)) {
            $updateData['pdf_cedula'] = $pdfCedulaPath;
        }
        if (isset($pdfCartaAceptacionPath)) {
            $updateData['carta_aceptacion'] = $pdfCartaAceptacionPath;
        }
        if (isset($pdfPapelVotacionPath)) {
            $updateData['pdf_papelvotacion'] = $pdfPapelVotacionPath;
        }
        if (isset($pdfTituloUniversidadPath)) {
            $updateData['pdf_titulouniversidad'] = $pdfTituloUniversidadPath;
        }
        if (isset($pdfHojavidaPath)) {
            $updateData['pdf_hojavida'] = $pdfHojavidaPath;
        }
        if (isset($pdfConadisPath)) {
            $updateData['pdf_conadis'] = $pdfConadisPath;
        }
        if (isset($pdfPagoMatriculaPath)) {
            $updateData['pago_matricula'] = $pdfPagoMatriculaPath;
        }

        $postulante->update($updateData);


        Notification::route('mail', $postulante->correo_electronico)
            ->notify(new Postulacion2($postulante));

        return redirect()->route('inicio')->with('success', 'Postulación realizada exitosamente.');
    }

    public function carta_aceptacionPdf(Request $request, $dni)
    {
        $postulante = Postulante::find($dni);

        $filename = 'Carta_de_Aceptacion_' . $postulante->nombre1 . '_' . $postulante->apellidop . '_' . $postulante->dni . '.pdf';

        return PDF::loadView('postulantes.carta_aceptacion', compact('postulante'))
                    ->setPaper('A4', 'portrait')
                    ->stream($filename);
    }



}
