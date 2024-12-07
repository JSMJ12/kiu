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

        // Crear el directorio si no existe
        Storage::makeDirectory('public/postulantes/pdf');

        $files = [
            'pdf_cedula' => 'pdf_cedula',
            'pdf_papelvotacion' => 'pdf_papelvotacion',
            'pdf_titulouniversidad' => 'pdf_titulouniversidad',
            'pdf_hojavida' => 'pdf_hojavida',
            'pdf_conadis' => 'pdf_conadis',
            'pago_matricula' => 'pago_matricula',
            'carta_aceptacion' => 'carta_aceptacion',
        ];

        $updateData = [];

        // Procesar cada archivo y actualizar solo si el archivo fue subido
        foreach ($files as $inputName => $column) {
            if ($request->hasFile($inputName)) {
                $updateData[$column] = $request->file($inputName)->store('postulantes/pdf', 'public');
            }
        }

        if (!empty($updateData)) {
            $postulante->update($updateData);
        }

        // Enviar notificación al postulante
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
