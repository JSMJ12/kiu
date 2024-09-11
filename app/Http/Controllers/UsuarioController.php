<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;


class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
    
        if ($request->ajax()) {
            \Log::info('Solicitud AJAX recibida');
            $data = User::with('roles')->select('users.*');
            return DataTables::of($data)
                ->addColumn('foto', function($row) {
                    return '<img src="' . asset($row->image) . '" alt="Imagen de ' . $row->name . '" style="max-width: 60px; border-radius: 50%;" loading="lazy">';
                })
                ->addColumn('roles', function ($row) {
                    return $row->roles->map(function ($role) {
                        return $role->name;
                    })->implode(', ');
                })
                ->addColumn('mensajeria', function ($row) {
                    return '<i class="fas fa-envelope send-message" data-toggle="modal" data-target="#sendMessageModal' . $row->id . '" title="Enviar mensaje"></i>
                            <!-- Modal de mensajes -->
                            <div class="modal fade" id="sendMessageModal' . $row->id . '" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel' . $row->id . '" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="sendMessageModalLabel' . $row->id . '">Enviar mensaje a ' . $row->name . '</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Formulario de envío de mensaje aquí -->
                                            <form id="sendMessageForm' . $row->id . '" action="' . route('messages.store') . '" method="POST" enctype="multipart/form-data">
                                                ' . csrf_field() . '
                                                <!-- Campos del formulario -->
                                                <div class="form-group">
                                                    <label for="message">Mensaje</label>
                                                    <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="attachment">Adjunto</label>
                                                    <input type="file" class="form-control-file" id="attachment" name="attachment">
                                                </div>
                                                <!-- Campo oculto para receiver_id -->
                                                <input type="hidden" name="receiver_id" value="' . $row->id . '">
                                                <!-- Fin de campos del formulario -->
                                                <button type="submit" class="btn btn-primary">Enviar</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin del modal -->';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('usuarios.edit', $row->id) . '" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>';
    
                    if ($row->status == 'ACTIVO') {
                        $btn .= '<form action="' . route('usuarios.disable', $row->id) . '" method="POST" style="display: inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Deshabilitar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>';
                    } else {
                        $btn .= '<form action="' . route('usuarios.enable', $row->id) . '" method="POST" style="display: inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Reactivar">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>';
                    }
    
                    return $btn;
                })
                ->rawColumns(['foto', 'mensajeria', 'action']) // Permitir HTML sin escapar para estas columnas
                ->make(true);
        }
    
        return view('usuarios.index', compact('perPage'));
    }
    
    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $usuario = new User();
    
        $usuario->name = $request->input('usu_nombre');
        $usuario->apellido = $request->input('usu_apellido');
        $usuario->sexo = $request->input('usu_sexo');
        $usuario->email = $request->input('email');
        $usuario->password = bcrypt($request->input('usu_contrasena'));
        $usuario->status = $request->input('usu_estatus', 'ACTIVO');
        $request->validate([
            'usu_foto' => 'nullable|image|max:2048', // Máximo tamaño 2MB
        ]);
        
        $primeraLetra = substr($usuario->name, 0, 1);
        
        // Almacenar la imagen
        if ($request->hasFile('usu_foto')) {
            $imagePath = $request->file('usu_foto')->store('public/imagenes_usuarios');
            $usuario->image = asset(str_replace('public/', 'storage/', $imagePath));
        } else {
            $usuario->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }

        $usuario->save();
        $usuario->roles()->sync($request->roles);
    
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }
    
    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }
    
    public function update(Request $request, User $usuario)
    {
        
        $usuario->name = $request->input('name'); 
        $usuario->apellido = $request->input('apellido');

        $usuario->save();
        $usuario->roles()->sync($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');

    }
    
    public function checkUserOneStatus()
    {
        $user1 = User::find(1);

        if ($user1 && $user1->status === 'INACTIVO') {
            User::where('id', '<>', 1)->update(['status' => 'INACTIVO']);
            return redirect()->route('usuarios.index')->with('success', 'Todos los usuarios han sido deshabilitados.');
        } else {
            User::where('id', '<>', 1)->update(['status' => 'INACTIVO']);
            return redirect()->route('usuarios.index')->with('success', 'Todos los usuarios han sido deshabilitados.');
        }
    }

    public function disable(User $usuario)
    {
        $this->checkUserOneStatus();
        if ($usuario->id !== 1) {
            $usuario->status = 'INACTIVO';
            $usuario->save();

            return redirect()->route('usuarios.index')->with('success', 'Usuario deshabilitado exitosamente.');
        } else {
            return redirect()->route('usuarios.index')->with('error', 'No se puede deshabilitar al usuario con ID 1.');
        }
    }
        
    public function enable(User $usuario)
    {
        $usuario->status = 'ACTIVO';
        $usuario->save();
    
        return redirect()->route('usuarios.index')->with('success', 'Usuario habilitado exitosamente.');
    }
    

}
