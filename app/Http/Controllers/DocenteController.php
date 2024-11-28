<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\User;
use App\Models\Nota;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\CalificacionVerificacion;

class DocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $docentes = Docente::query();

            return datatables()->eloquent($docentes)
                ->addColumn('foto', function ($docente) {
                    return '<img src="' . asset('storage/' . $docente->image) . '" 
                        alt="Imagen de ' . $docente->nombre1 . '" 
                        style="max-width: 60px; border-radius: 50%;">';
                })
                ->addColumn('nombre_completo', function ($docente) {
                    return $docente->nombre1 . '<br>' . $docente->nombre2 . '<br>' .
                        $docente->apellidop . '<br>' . $docente->apellidom;
                })
                ->addColumn('acciones', function ($docente) {
                    $acciones = '<div style="display: flex; gap: 10px; align-items: center;">
                                <a href="' . route('docentes.edit', $docente->dni) . '" 
                                   class="btn btn-primary custom-btn btn-sm" title="Editar">
                                   <i class="fas fa-edit"></i>
                                </a>
                                <a href="' . route('asignaturas_docentes.create1', $docente->dni) . '" 
                                   class="btn btn-success custom-btn btn-sm" title="Agregar Asignaturas">
                                   <i class="fas fa-plus"></i>
                                </a>
                                <a href="' . route('cohortes_docentes.create1', $docente->dni) . '" 
                                   class="btn btn-warning custom-btn btn-sm" title="Agregar Cohortes">
                                   <i class="fas fa-plus"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm btn-modal-asignatura" 
                                          data-id="' . $docente->dni . '" 
                                          data-type="asignaturas" 
                                          title="Ver Asignaturas">
                                          <i class="fas fa-eye"></i>
                                      </button>
                                <button type="button" class="btn btn-info btn-sm btn-modal-cohortes" 
                                        data-dni="' . $docente->dni . '" 
                                        data-type="cohortes" 
                                        title="Ver Cohortes">
                                        <i class="fas fa-eye"></i>
                                    </button>
                             </div>';

                    return $acciones;
                })

                ->rawColumns(['foto', 'nombre_completo', 'acciones']) // Permitir contenido HTML
                ->toJson();
        }

        return view('docentes.index');
    }

    public function cargarAsignaturas($dni)
    {
        $docente = Docente::with('asignaturas')->where('dni', $dni)->first();
        return response()->json($docente ? $docente->asignaturas : []);
    }

    public function obtenerCohortes($dni)
    {
        // Buscar el docente por su DNI y cargar las relaciones necesarias
        $docente = Docente::where('dni', $dni)
            ->with(['cohortes' => function ($query) {
                // Asegurar que solo se carguen cohortes únicas
                $query->distinct();
            }, 'cohortes.asignaturas', 'cohortes.maestria', 'cohortes.aula'])
            ->first();

        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado.'], 404);
        }

        // Preparar los datos de los cohortes y asignaturas
        $cohortes = $docente->cohortes->map(function ($cohorte) use ($docente) {
            // Evitar duplicados y procesar la información
            return [
                'id' => $cohorte->id,
                'nombre' => $cohorte->nombre,
                'modalidad' => $cohorte->modalidad,
                'aula' => $cohorte->aula ? $cohorte->aula->nombre : 'No disponible',
                'paralelo' => $cohorte->aula && $cohorte->aula->paralelo ? $cohorte->aula->paralelo : 'No disponible',
                'maestria' => $cohorte->maestria->nombre,
                'asignaturas' => $cohorte->asignaturas->map(function ($asignatura) use ($docente, $cohorte) {
                    $calificacion = $asignatura->calificacionVerificaciones()
                        ->where('docente_dni', $docente->dni)
                        ->where('cohorte_id', $cohorte->id)
                        ->first();

                    return [
                        'id' => $asignatura->id,
                        'nombre' => $asignatura->nombre,
                        'calificado' => $calificacion ? ($calificacion->calificado ? 'Calificado' : 'No calificado') : 'No calificado',
                        'editar' => $calificacion ? $calificacion->editar : false,
                    ];
                }),
            ];
        });

        // Incluir el nombre del docente en la respuesta
        return response()->json([
            'docente_nombre' => $docente,  // Aquí se pasa el nombre completo del docente
            'cohortes' => $cohortes
        ]);
    }



    public function guardarCambios(Request $request)
    {
        // Recuperar los datos del formulario
        $docenteDni = $request->input('docente_dni');
        $permisosEditar = $request->input('permiso_editar', []);

        // Iterar sobre los cohortes y asignaturas dentro de 'permiso_editar'
        foreach ($permisosEditar as $cohorteId => $asignaturas) {
            foreach ($asignaturas as $asignaturaId => $value) {
                // Convertir el valor a booleano
                $editar = $value == '1' ? true : false;

                // Buscar la calificación en la base de datos y actualizarla
                CalificacionVerificacion::updateOrCreate(
                    [
                        'docente_dni' => $docenteDni,
                        'asignatura_id' => $asignaturaId,
                        'cohorte_id' => $cohorteId
                    ],
                    [
                        'editar' => $editar
                    ]
                );
            }
        }

        // Redireccionar de vuelta o realizar otra acción después de guardar los cambios
        return redirect()->back()->with('success', 'Cambios guardados exitosamente');
    }



    public function create()
    {
        return view('docentes.create');
    }

    public function store(Request $request)
    {
        $docente = new Docente;
        $docente->nombre1 = $request->input('nombre1');
        $docente->nombre2 = $request->input('nombre2');
        $docente->apellidop = $request->input('apellidop');
        $docente->apellidom = $request->input('apellidom');
        $docente->contra = bcrypt($request->input('dni')); // Encriptar la contraseña
        $docente->sexo = $request->input('sexo');
        $docente->dni = $request->input('dni');
        $docente->tipo = $request->input('tipo');
        $docente->email = $request->input('email');
        $request->validate([
            'docen_foto' => 'nullable|image|max:2048', //máximo tamaño 2MB
        ]);
        $primeraLetra = substr($docente->nombre1, 0, 1);
        if ($request->hasFile('docen_foto')) {
            $path = $request->file('docen_foto')->store('imagenes_usuarios', 'public');
            $docente->image = $path;
        } else {
            $docente->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }
        //Almacenar la imagen
        $docente->save();

        $usuario = new User;
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('email');
        $usuario->image = $docente->image;
        $docenteRole = Role::findById(2);
        $usuario->assignRole($docenteRole);
        $usuario->save();



        return redirect()->route('docentes.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($dni)
    {
        $docente = Docente::where('dni', $dni)->first();
        return view('docentes.edit', compact('docente'));
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::findOrFail($id);
        $docente->nombre1 = $request->input('nombre1');
        $docente->nombre2 = $request->input('nombre2');
        $docente->apellidop = $request->input('apellidop');
        $docente->apellidom = $request->input('apellidom');
        $docente->sexo = $request->input('sexo');
        $docente->dni = $request->input('dni');
        $docente->tipo = $request->input('tipo');
        $docente->email = $request->input('email');

        $request->validate([
            'docen_foto' => 'nullable|image|max:2048', // Máximo tamaño 2MB
        ]);

        if ($request->hasFile('docen_foto')) {
            // Eliminar la imagen anterior si existe
            if ($docente->image) {
                \Storage::disk('public')->delete($docente->image);
            }

            $path = $request->file('docen_foto')->store('imagenes_usuarios', 'public');
            $docente->image = $path;
        }

        $docente->save();

        $usuario = User::where('email', $docente->email)->firstOrFail();
        $usuario->name = $docente->nombre1;
        $usuario->apellido = $docente->apellidop;
        $usuario->sexo = $docente->sexo;
        $usuario->email = $docente->email;
        $usuario->image = $docente->image;
        $usuario->save();

        return redirect()->route('docentes.index')->with('success', 'Docente actualizado exitosamente.');
    }
}
