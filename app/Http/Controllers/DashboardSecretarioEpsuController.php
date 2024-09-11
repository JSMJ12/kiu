<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pago;

class DashboardSecretarioEpsuController extends Controller
{   public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $hoy = now()->startOfDay();
        $mes = now()->startOfMonth();
        $anio = now()->startOfYear();
    
        // Todos los pagos
        $todosPagos = Pago::with('alumno.matriculas')->orderBy('fecha_pago')->get();
    
        // Pagos agrupados por cohorte
        $pagosPorCohorte = $todosPagos->groupBy(function($pago) {
            return optional($pago->alumno->matriculas->first())->cohorte_id;
        });
        // Monto de pagos por cohorte
        $montoPorCohorte = $pagosPorCohorte->map->sum('monto');
        
        // Cantidad de pagos por cohorte
        $cantidadPorCohorte = $pagosPorCohorte->map->count();
    
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
            'montoPorCohorte' => $montoPorCohorte,
            'cantidadPorCohorte' => $cantidadPorCohorte,
        ], compact('perPage'));
    }
    

}
