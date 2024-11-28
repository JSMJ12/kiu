<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Postulante;
use App\Models\User;
use App\Notifications\SubirArchivoNotification;
class DispararRecordatorioSubirArchivos extends Command
{
    protected $signature = 'recordatorio:subirarchivos';

    protected $description = 'Disparar recordatorio para subir archivos a los postulantes';

    public function handle()
    {
        $postulantes = Postulante::all();

        foreach ($postulantes as $postulante) {
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
        }
    }
    
}

