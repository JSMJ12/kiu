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
        $perPage = $request->input('perPage', 10);

        // Obtener todos los docentes
        $docentes = Docente::all();

        return view('docentes.index', compact('docentes', 'perPage'));
    }

    public function guardarCambios(Request $request)
    {
        // Recuperar los datos del formulario
        $docenteDni = $request->input('docente_dni');
        $permisosEditar = $request->input('permiso_editar', []);
    
        // Iterar sobre todas las combinaciones posibles de asignaturas y cohortes
        foreach ($permisosEditar as $dni => $asignaturas) {
            foreach ($asignaturas as $asignaturaId => $cohortes) {
                foreach ($cohortes as $cohorteId => $value) {
                    // Convertir el valor a booleano
                    $editar = $value == '1' ? true : false;
    
                    // Buscar la calificación en la base de datos y actualizarla
                    CalificacionVerificacion::updateOrCreate(
                        [
                            'docente_dni' => $dni,
                            'asignatura_id' => $asignaturaId,
                            'cohorte_id' => $cohorteId
                        ],
                        [
                            'editar' => $editar
                        ]
                    );
                }
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
            $image = $request->file('docen_foto')->store('public/imagenes_usuarios');
            $docente->image = url(str_replace('public/', 'storage/', $image));
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
    
    public function update(Request $request, Docente $docente)
    {
        $docente->nombre1 = $request->input('nombre1');
        $docente->apellidop = $request->input('apellidop');
        $docente->tipo = $request->input('tipo');
        $docente->save();
    
        return redirect()->route('docentes.index')->with('success', 'Usuario actualizado exitosamente.');
    }
    
}