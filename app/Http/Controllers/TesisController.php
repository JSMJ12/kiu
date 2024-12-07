<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Docente;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\TesisAceptadaNotificacion;
use Yajra\DataTables\DataTables;

class TesisController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente || !$docente->maestria()->exists()) {
            return $request->ajax()
                ? response()->json(['error' => 'No estás asignado a ninguna maestría.'], 403)
                : redirect()->back()->withErrors(['error' => 'No estás asignado a ninguna maestría.']);
        }

        $maestriaId = $docente->maestria()->first()->id;

        if ($request->ajax()) {
            $solicitudes = Tesis::with('alumno', 'tutor')
                ->whereHas('alumno', function ($query) use ($maestriaId) {
                    $query->where('maestria_id', $maestriaId);
                })
                ->orderByRaw('tutor_dni IS NULL DESC')
                ->orderBy('estado', 'asc')
                ->get();

            return DataTables::of($solicitudes)
                ->addColumn('nombre_completo', function ($tesis) {
                    return $tesis->alumno
                        ? $tesis->alumno->nombre1 . ' ' . $tesis->alumno->nombre2 . ' ' . $tesis->alumno->apellidop . ' ' . $tesis->alumno->apellidom
                        : 'Sin alumno asignado';
                })
                ->addColumn('acciones', function ($tesis) {
                    return view('partials.botones_tesis', compact('tesis'))->render();
                })
                ->editColumn('estado', function ($tesis) {
                    $badgeClass = $tesis->estado === 'aprobado' ? 'success' : 'warning';
                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($tesis->estado) . '</span>';
                })
                ->addColumn('alumno_image', function ($tesis) {
                    return $tesis->alumno && $tesis->alumno->image
                        ? asset('storage/' . $tesis->alumno->image)
                        : 'default.jpg'; // Si no tiene imagen, usa una imagen predeterminada
                })
                ->rawColumns(['acciones', 'estado'])
                ->make(true);
        }

        $docentes = Docente::all();
        return view('titulacion.solicitudes', compact('docentes'));
    }
    public function aceptarTema($id)
    {
        try {
            $tesis = Tesis::findOrFail($id);

            // Verificar si el usuario está asignado
            if (!$tesis->alumno) {
                \Log::error('No se encontró alumno para la tesis con ID: ' . $id);
                return response()->json(['error' => 'No hay usuario asignado a esta tesis.'], 400);
            }

            $usuario = User::where('email', $tesis->alumno->email_institucional)->first();

            if ($usuario) {
                // Pasar tanto la tesis como el usuario al constructor de la notificación
                Notification::route('mail', $usuario->email)
                    ->notify(new TesisAceptadaNotificacion($tesis, $usuario));
            }

            $tesis->estado = 'aprobado';
            $tesis->save();

            return response()->json(['success' => 'Tema aceptado correctamente.']);
        } catch (\Exception $e) {
            \Log::error('Error al aceptar tema: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error en el servidor.'], 500);
        }
    }
    public function asignarTutor(Request $request, $id)
    {
        $tesis = Tesis::findOrFail($id);
        $dni = $request->input('dni');
        $docente = Docente::where('dni', $dni)->first();
        $tutor = User::where('email', $docente->email)->first();  // Corregido aquí

        // Verificar si el docente y el tutor existen
        if ($docente && $tutor) {
            $tutor->assignRole('Tutor');  // Asignar el rol de tutor
            $tesis->tutor_dni = $docente->dni;  // Asignar el tutor al campo correspondiente
            $tesis->save();  // Guardar la tesis con el nuevo tutor asignado

            // Redirigir a la página anterior con un mensaje de éxito
            return redirect()->back()->with('success', 'Tutor asignado correctamente.');
        } else {
            // Redirigir a la página anterior con un mensaje de error
            return redirect()->back()->with('error', 'No se encontró el tutor o el docente.');
        }
    }
    public function rechazarTema($id)
    {
        $tesis = Tesis::findOrFail($id);
        $tesis->estado = 'rechazado';
        $tesis->save();

        return response()->json(['success' => 'Tema rechazado correctamente.']);
    }


    public function store(Request $request)
    {
        // Obtener el alumno asociado al usuario autenticado
        $alumno = Alumno::where('email_institucional', Auth::user()->email)->first();

        if (!$alumno) {
            return redirect()->route('tesis.create')->with('error', 'Alumno no encontrado. Verifique que su correo institucional esté registrado.');
        }

        // Paso 1: Validación de los datos del tema y descripción
        $validatedData = $request->validate([
            'tema' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        // Verifica si el paso 2 ya fue enviado (se cargó el archivo PDF)
        if ($request->hasFile('solicitud_pdf')) {
            // Paso 2: Validación del archivo PDF
            $request->validate([
                'solicitud_pdf' => 'required|file|mimes:pdf|max:2048',
            ]);

            // Subir el archivo PDF de solicitud
            $pdfPath = $request->file('solicitud_pdf')->store('solicitudes_pdf', 'public');

            // Crear una nueva entrada en la tabla 'tesis'
            $tesis = new Tesis();
            $tesis->alumno_dni = $alumno->dni; // Usar el DNI del alumno autenticado
            $tesis->tema = $validatedData['tema'];
            $tesis->descripcion = $validatedData['descripcion'];
            $tesis->solicitud_pdf = $pdfPath;
            $tesis->estado = 'pendiente'; // Estado inicial del proceso
            $tesis->save();

            return redirect()->route('dashboard_alumno')->with('success', 'Solicitud de aprobación de tema enviada correctamente.');
        }

        // Si no se ha llegado al paso 2, solo se valida el tema y descripción
        return redirect()->route('tesis.create')->with('warning', 'Por favor complete todos los pasos del formulario.');
    }

    public function create()
    {
        $email = Auth::user()->email;
        $alumno = Alumno::where('email_institucional', $email)->first();
        $dniAlumno = $alumno->dni;

        $tesis = Tesis::where('alumno_dni', $dniAlumno)->with('tutorias')->first();
    
        return view('titulacion.proceso', compact('tesis'));
    }


    public function downloadPDF()
    {
        $alumno = Alumno::where('email_institucional', Auth::user()->email)->first();

        if (!$alumno) {
            abort(404, 'Alumno no encontrado');
        }

        $filename = 'Tema_Tesis_' . $alumno->nombre1 . '_' . $alumno->apellidop . '_' . $alumno->dni . '.pdf';
        $coordinadorDni = $alumno->maestria->coordinador;

        // Buscar al docente utilizando el DNI
        $coordinador = Docente::where('dni', $coordinadorDni)->first();

        if ($coordinador) {
            // Acceder al nombre completo utilizando el método getFullNameAttribute
            $nombreCompleto = $coordinador->getFullNameAttribute();
        } else {
            $nombreCompleto = 'Coordinador no encontrado';
        }

        return PDF::loadView('titulacion.solicitud', compact('alumno', 'nombreCompleto'))
            ->setPaper('A4', 'portrait')
            ->download($filename); // Descargar directamente
    }
    public function show($id)
    {
        $tesis = Tesis::with('alumno', 'tutor')->findOrFail($id);

        return view('tesis.show', compact('tesis'));
    }
}
