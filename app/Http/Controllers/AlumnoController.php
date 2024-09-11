<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Secretario;
use Spatie\Permission\Models\Role;

class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();

        if ($user->hasRole('Administrador')) {
            $alumnos = Alumno::all();
        } else {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $alumnos = Alumno::whereIn('maestria_id', $maestriasIds)->get();
        }
        

        return view('alumnos.index', compact('alumnos', 'perPage'));
    }

    public function create()
    {
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        $user = auth()->user();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)
                ->where('status', 'ACTIVO')
                ->get();
        } else {
            $maestrias = Maestria::where('status', 'ACTIVO')->get();
        }

        return view('alumnos.create', compact('provincias', 'maestrias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maestria_id' => 'required|exists:maestrias,id',
            'image' => 'nullable|image|max:2048', // máximo tamaño 2MB
        ]);

        // Obtener el ID de la maestría
        $maestriaId = $request->input('maestria_id');
        
        // Obtener la maestría y su arancel
        $maestria = Maestria::findOrFail($maestriaId);
        $arancel = $maestria->arancel;

        // Obtener el próximo número de registro
        $nuevoRegistro = Alumno::where('maestria_id', $maestriaId)->count() + 1;

        // Crear un nuevo objeto Alumno
        $alumno = new Alumno;
        $alumno->nombre1 = $request->input('nombre1');
        $alumno->nombre2 = $request->input('nombre2');
        $alumno->apellidop = $request->input('apellidop');
        $alumno->apellidom = $request->input('apellidom');
        $alumno->contra = bcrypt($request->input('dni')); // Encriptar la contraseña
        $alumno->sexo = $request->input('sexo');
        $alumno->dni = $request->input('dni');
        $alumno->email_institucional = $request->input('email_ins');
        $alumno->email_personal = $request->input('email_per');
        $alumno->estado_civil = $request->input('estado_civil');
        $alumno->fecha_nacimiento = $request->input('fecha_nacimiento');
        $alumno->provincia = $request->input('provincia');
        $alumno->canton = $request->input('canton');
        $alumno->barrio = $request->input('barrio');
        $alumno->direccion = $request->input('direccion');
        $alumno->nacionalidad = $request->input('nacionalidad');
        $alumno->etnia = $request->input('etnia');
        $alumno->carnet_discapacidad = $request->input('carnet_discapacidad');
        $alumno->tipo_discapacidad = $request->input('tipo_discapacidad');
        $alumno->maestria_id = $request->input('maestria_id');
        $alumno->porcentaje_discapacidad = $request->input('porcentaje_discapacidad');
        $alumno->registro = $nuevoRegistro;
        $alumno->monto_total = $arancel; // Asignar el valor del arancel

        // Procesar la imagen
        $primeraLetra = substr($alumno->nombre1, 0, 1);
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/imagenes_usuarios');
            $alumno->image = url(str_replace('public/', 'storage/', $image));
        } else {
            $alumno->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }

        // Almacenar el alumno
        $alumno->save();

        // Crear un nuevo objeto User
        $usuario = new User;
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('email_ins');
        $usuario->image = $alumno->image;
        $alumnoRole = Role::findById(4);
        $usuario->assignRole($alumnoRole);
        $usuario->save();

        return redirect()->route('alumnos.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($dni)
    {
        $maestrias = Maestria::where('status', 'ACTIVO')->get();
        $alumno = Alumno::where('dni', $dni)->firstOrFail();
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        return view('alumnos.edit', compact('alumno', 'provincias', 'maestrias'));
    }

    public function update(Request $request, $dni)
    {
        // Obtener el ID de la maestría
        $maestriaId = $request->input('maestria_id');
        
        // Obtener la maestría y su arancel
        $maestria = Maestria::findOrFail($maestriaId);
        $arancel = $maestria->arancel;
        $alumno = Alumno::where('dni', $dni)->firstOrFail();
        $alumno->nombre1 = $request->input('nombre1');
        $alumno->nombre2 = $request->input('nombre2');
        $alumno->apellidop = $request->input('apellidop');
        $alumno->apellidom = $request->input('apellidom');
        $alumno->dni = $request->input('dni');
        $alumno->estado_civil = $request->input('estado_civil');
        $alumno->fecha_nacimiento = $request->input('fecha_nacimiento');
        $alumno->provincia = $request->input('provincia');
        $alumno->canton = $request->input('canton');
        $alumno->barrio = $request->input('barrio');
        $alumno->direccion = $request->input('direccion');
        $alumno->nacionalidad = $request->input('nacionalidad');
        $alumno->etnia = $request->input('etnia');
        $alumno->email_personal = $request->input('email_personal');
        $alumno->email_institucional = $request->input('email_institucional');
        $alumno->carnet_discapacidad = $request->input('carnet_discapacidad');
        $alumno->tipo_discapacidad = $request->input('tipo_discapacidad');
        $alumno->porcentaje_discapacidad = $request->input('porcentaje_discapacidad');
        $alumno->sexo = $request->input('sexo');
        $alumno->maestria_id = $request->input('maestria_id');
        $alumno->monto_total = $arancel; 
        $alumno->save();

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente');
    }
}
