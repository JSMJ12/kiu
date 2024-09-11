<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\User;
Use App\Models\Secretario;
Use App\Models\Maestria;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
class SecretarioController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $secretarios = Secretario::with('seccion')->get();
        $maestrias = Maestria::all();
        return view('secretarios.index', compact('secretarios', 'maestrias', 'perPage'));
    }
    
    public function create()
    {
        $secciones = DB::table('secciones')
             ->whereNotIn('id', function($query) {
                 $query->select('seccion_id')
                       ->from('secretarios')
                       ->whereNotNull('seccion_id');
             })
             ->get();
        return view('secretarios.create', compact('secciones'));
    }
    
    public function store(Request $request)
    {
        $secretario = new Secretario;
        $secretario->nombre1 = $request->input('nombre1');
        $secretario->nombre2 = $request->input('nombre2');
        $secretario->apellidop = $request->input('apellidop');
        $secretario->apellidom = $request->input('apellidom');
        $secretario->contra = bcrypt($request->input('dni')); // Encriptar la contraseña
        $secretario->sexo = $request->input('sexo');
        $secretario->dni = $request->input('dni');
        $secretario->email = $request->input('email');
        $request->validate([
            'image' => 'nullable|image|max:2048', //máximo tamaño 2MB
        ]);
        $primeraLetra = substr($secretario->nombre1, 0, 1);
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/imagenes_usuarios');
            $secretario->image = url(str_replace('public/', 'storage/', $image));
        } else {
            $secretario->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }

        $secretario->seccion_id = $request->input('seccion_id');
        $secretario->save();
        
        $usuario = new User;
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('email');
        $usuario->image = $secretario->image;
        $secretarioRole = Role::findById(3);
        $usuario->assignRole($secretarioRole);
        $usuario->save();
        
        

        return redirect()->route('secretarios.index')->with('success', 'Usuario creado exitosamente.');
    }
    
    public function edit($id)
    {
        $secretario = Secretario::find($id);
        return view('secretarios.edit', compact('secretario'));
    }
    
    public function update(Request $request, Secretario $secretario)
    {
        $secretario->nombre1 = $request->input('nombre1');
        $secretario->apellidop = $request->input('apellidop');
        $secretario->save();
    
        return redirect()->route('secretarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }
}
