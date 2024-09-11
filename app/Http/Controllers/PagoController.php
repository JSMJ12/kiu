<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Alumno;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $hoy = now()->startOfDay();
        $mes = now()->startOfMonth();
        $anio = now()->startOfYear();
    
        // Todos los pagos
        $todosPagos = Pago::orderBy('fecha_pago')->get();
    
        // Pagos por hora del día
        $pagosPorHora = $todosPagos->where('fecha_pago', '>=', $hoy)->groupBy(function($pago) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->format('H:00');
        })->map->sum('monto');
    
        // Pagos por día del mes
        $pagosPorDia = $todosPagos->where('fecha_pago', '>=', $mes)->groupBy(function($pago) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->format('d');
        })->map->sum('monto');
    
        // Pagos por mes del año
        $pagosPorMes = $todosPagos->where('fecha_pago', '>=', $anio)->groupBy(function($pago) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->format('F');
        })->map->sum('monto');
    
        // Cantidad de pagos realizados
        $cantidadPorHora = $todosPagos->where('fecha_pago', '>=', $hoy)->groupBy(function($pago) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->format('H:00');
        })->map->count();
    
        $cantidadPorDia = $todosPagos->where('fecha_pago', '>=', $mes)->groupBy(function($pago) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->format('d');
        })->map->count();
    
        $cantidadPorMes = $todosPagos->where('fecha_pago', '>=', $anio)->groupBy(function($pago) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->format('F');
        })->map->count();
    
        // Pagos por verificar
        $pagosPorVerificar = Pago::where('verificado', false)->count();
    
        return view('pagos.index', [
            'pagosPorHora' => $pagosPorHora,
            'pagosPorDia' => $pagosPorDia,
            'pagosPorMes' => $pagosPorMes,
            'todosPagos' => $todosPagos,
            'cantidadPorHora' => $cantidadPorHora,
            'cantidadPorDia' => $cantidadPorDia,
            'cantidadPorMes' => $cantidadPorMes,
            'pagosPorVerificar' => $pagosPorVerificar,
            'alumnosPendientes' => Pago::where('verificado', false)->with('alumno')->get()->unique('alumno_id'),
        ], compact('perPage'));
    }
    

    public function pago()
    {
        $user = Auth::user();

        // Buscar al alumno
        $alumno = Alumno::where('nombre1', $user->name)
                        ->where('email_institucional', $user->email)
                        ->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        $maestria = $alumno->maestria;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }

        // Calcular descuento y total a pagar según el tipo de descuento del alumno
        $descuento = 0;
        $total_pagar = 0;

        if ($alumno->descuento == 'academico') {
            $descuento = $maestria->arancel * 0.30;
            $total_pagar = $maestria->arancel * 0.70;
        } elseif ($alumno->descuento == 'socioeconomico') {
            $descuento = $maestria->arancel * 0.20;
            $total_pagar = $maestria->arancel * 0.80;
        } elseif ($alumno->descuento == 'graduados') {
            $descuento = $maestria->arancel * 0.20;
            $total_pagar = $maestria->arancel * 0.80;
        } elseif ($alumno->descuento == 'mejor_graduado') {
            $descuento = $maestria->arancel * 1;
            $total_pagar = 0;
        }

        $programa = [
            'nombre' => $maestria->nombre,
            'arancel' => $maestria->arancel,
            'descuento' => $descuento,
            'total_pagar' => $total_pagar,
        ];

        $pagos = $alumno->pagos; 

        return view('pagos.pago', compact('programa', 'alumno', 'pagos'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|exists:alumnos,dni',
            'modalidad_pago' => 'required|string|in:unico,trimestral',
            'fecha_pago' => 'required|date',
            'archivo_comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4048',
        ]);
    
        $alumno = Alumno::where('dni', $request->dni)->first();
    
        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }
    
        // Obtener el monto total previamente calculado
        $total_pagar = $alumno->monto_total;
    
        // Ajustar el monto a pagar según la modalidad seleccionada
        $monto_pagar = $request->monto;
    
        // Guardar el archivo comprobante
        $archivo_comprobante = $request->file('archivo_comprobante');
        $archivo_path = $archivo_comprobante->store('comprobantes', 'public');
    
        // Crear el registro de pago usando el método create
        $pago = Pago::create([
            'dni' => $alumno->dni,
            'monto' => $monto_pagar,
            'fecha_pago' => $request->fecha_pago,
            'archivo_comprobante' => $archivo_path,
            'modalidad_pago' => $request->modalidad_pago
        ]);
    
        // Redirigir con un mensaje de éxito
        return redirect()->route('inicio')->with('success', 'Pago realizado exitosamente.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_pago' => 'required|date',
            'pagado' => 'required|boolean',
        ]);

        $pago = Pago::findOrFail($id);
        $pago->update($request->all());

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado con éxito.');
    }
    public function showDescuentoForm()
    {
        $user = Auth::user();

        // Busca al alumno
        $alumno = Alumno::where('nombre1', $user->name)
                        ->where('email_institucional', $user->email)
                        ->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        // Obtén la maestría del alumno
        $maestria = $alumno->maestria;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }
        $programa = [
            'nombre' => $maestria->nombre,
            'arancel' => $maestria->arancel,
            'descuento_academico' => $maestria->arancel * 0.30,
            'descuento_socioeconomico' => $maestria->arancel * 0.20,
            'descuento_graduados' => $maestria->arancel * 0.20,
            'descuento_mejor_graduado' => $maestria->arancel * 1.00,
            'total_con_academico' => $maestria->arancel * 0.70,
            'total_con_socioeconomico' => $maestria->arancel * 0.80,
            'total_con_graduados' => $maestria->arancel * 0.80,
            'total_con_mejor_graduado' => 0
        ];

        return view('pagos.descuento', compact('programa', 'alumno'));
    }
    public function processDescuento(Request $request)
    {
        // Verifica si el usuario está autenticado
        if (!$user = Auth::user()) {
            return redirect()->back()->with('error', 'Usuario no autenticado.');
        }

        // Busca al alumno por el usuario autenticado
        $alumno = Alumno::where('nombre1', $user->name)
                        ->where('email_institucional', $user->email)
                        ->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        $request->validate([
            'descuento' => 'required',
        ]);

        // Guardar el descuento seleccionado en el alumno
        $alumno->descuento = $request->input('descuento');

        // Guardar el documento de autenticidad si existe
        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('documentos_autenticidad', 'public');
            $alumno->documento_autenticidad = $documentoPath;
        }

        // Obtener la maestría del alumno
        $maestria = $alumno->maestria;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }

        // Calcular el monto total a pagar basado en el descuento
        $arancel = $maestria->arancel;
        $descuento = 0;

        switch ($alumno->descuento) {
            case 'academico':
                $descuento = $arancel * 0.30;
                break;
            case 'socioeconomico':
                $descuento = $arancel * 0.20;
                break;
            case 'graduados':
                $descuento = $arancel * 0.20;
                break;
            case 'mejor_graduado':
                $descuento = $arancel; // 100% descuento
                break;
            default:
                $descuento = 0; // Sin descuento
                break;
        }

        // Calcular el monto total después de aplicar el descuento
        $monto_total = $arancel - $descuento;

        // Guardar el monto total calculado en el campo 'monto_total' del alumno
        $alumno->monto_total = $monto_total;

        $alumno->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('inicio');
    }
    public function verificar_pago($id)
    {
        // Encontrar el pago por su ID
        $pago = Pago::findOrFail($id);
        
        // Encontrar el alumno por el DNI del pago
        $alumno = Alumno::where('dni', $pago->dni)->first();
        if (!$alumno) {
            return redirect()->route('pagos.index')->with('error', 'Alumno no encontrado.');
        }

        // Restar el monto del pago del monto total del alumno
        $nuevo_monto_total = $alumno->monto_total - $pago->monto;
        // Asegurarse de que el nuevo monto total no sea negativo
        if ($nuevo_monto_total < 0) {
            return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto total del alumno.');
        }

        // Actualizar el monto total del alumno
        $alumno->update(['monto_total' => $nuevo_monto_total]);
        // Actualizar el campo verificado del pago a true
        $pago->update(['verificado' => true]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('pagos.index')->with('success', 'Pago verificado con éxito y monto total actualizado.');
    }


}
