@extends('adminlte::page')
@section('title', 'Dashboard Postulante')
@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <img src="{{ asset('images/unesum.png') }}" alt="University Logo" class="logo">
                        <img src="{{ asset('images/posgrado-21.png') }}" alt="University Seal" class="seal">
                        <div class="university-info text-center d-flex align-items-center flex-column">
                            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span>
                            <span class="institute">INSTITUTO DE POSGRADO</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">Maestría</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>{{ $postulante->maestria->nombre }}</strong></td>
                                        <td><span class="text-muted">Precio de Matriculación:</span> ${{ $postulante->maestria->matricula }}</td>
                                    </tr>
                                    @if ($postulante->maestria->cohorte)
                                    @php
                                        // Obtenemos la fecha actual
                                        $fecha_actual = now();
                                        
                                        // Filtramos los cohortes cuya fecha de inicio esté dentro de los próximos 5 días
                                        $cohortes_filtrados = $postulante->maestria->cohorte->filter(function ($cohorte) use ($fecha_actual) {
                                            return $cohorte->fecha_inicio >= $fecha_actual && $cohorte->fecha_inicio <= $fecha_actual->addDays(5);
                                        });
                                        
                                        // Si no hay cohortes dentro de los próximos 5 días
                                        if ($cohortes_filtrados->isEmpty()) {
                                            // Buscamos el siguiente cohorte más cercano en el tiempo que esté al menos a 10 días de distancia
                                            $cohortes_filtrados = $postulante->maestria->cohorte->sortBy('fecha_inicio')->filter(function ($cohorte) use ($fecha_actual) {
                                                return $cohorte->fecha_inicio > $fecha_actual->addDays(5);
                                            })->take(1);
                                        }
                                    @endphp
                                
                                        @foreach($cohortes_filtrados as $cohorte)
                                            <tr>
                                                <td><span class="text-muted">Inicio:</span> {{ $cohorte->fecha_inicio ?? 'N/A' }}</td>
                                                <td><span class="text-muted">Fin:</span> {{ $cohorte->fecha_fin ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2">No hay cohortes asociados a esta maestría.</td>
                                        </tr>
                                    @endif
                                </tbody>
                                
                            </table>
                        </div>                        
                        @if ($postulante->status == 0 && ($postulante->pdf_cedula !== null || $postulante->pdf_papelvotacion !== null || $postulante->pdf_titulouniversidad !== null || $postulante->pdf_hojavida !== null || ($postulante->discapacidad == 'Si' && $postulante->pdf_conadis !== null)))
                            <div class="alert alert-info text-center" role="alert">
                                Su solicitud está en proceso de revisión. Por favor, espere mientras verificamos sus archivos.
                            </div>
                        @endif
                        @if ($postulante->status == 1 && ($postulante->pdf_cedula !== null || $postulante->pdf_papelvotacion !== null || $postulante->pdf_titulouniversidad !== null || $postulante->pdf_hojavida !== null || ($postulante->discapacidad == 'Si' && $postulante->pdf_conadis !== null)) && $postulante->pago_matricula !== null)
                            <div class="alert alert-info text-center" role="alert">
                                Su solicitud está en proceso de revisión. Por favor, espere mientras verificamos sus archivos.
                            </div>
                        @endif
                        <form action="{{ route('dashboard_postulante.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            @if (is_null($postulante->pdf_cedula))
                                <div class="form-group">
                                    <label for="pdf_cedula">PDF Cédula / Pasaporte:</label>
                                    <input type="file" name="pdf_cedula" class="form-control-file" accept=".pdf">
                                </div>
                            @endif

                            @if (is_null($postulante->pdf_papelvotacion))
                                <div class="form-group">
                                    <label for="pdf_papelvotacion">PDF Papel de Votación:</label>
                                    <input type="file" name="pdf_papelvotacion" class="form-control-file" accept=".pdf">
                                </div>
                            @endif

                            @if (is_null($postulante->pdf_titulouniversidad))
                                <div class="form-group">
                                    <label for="pdf_titulouniversidad">PDF Título de Universidad:</label>
                                    <input type="file" name="pdf_titulouniversidad" class="form-control-file" accept=".pdf">
                                </div>
                            @endif

                            @if (is_null($postulante->pdf_hojavida))
                                <div class="form-group">
                                    <label for="pdf_hojavida">PDF Hoja de Vida:</label>
                                    <input type="file" name="pdf_hojavida" class="form-control-file" accept=".pdf">
                                </div>
                            @endif

                            @if($postulante->discapacidad == 'Sí' && is_null($postulante->pdf_conadis))
                                <div class="form-group" id="divPDFConadis">
                                    <label for="pdf_conadis">PDF CONADIS:</label>
                                    <input type="file" name="pdf_conadis" class="form-control-file" accept=".pdf">
                                </div>
                            @endif
                            
                            @if (is_null($postulante->carta_aceptacion))
                                <a href="{{ route('postulantes.carta_aceptacion', $postulante->dni) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download"></i> Descargar el formato de la  Carta de Aceptación
                                </a>
                                <div class="form-group">
                                    <label for="carta_aceptacion">Carta de Aceptación:</label>
                                    <input type="file" name="carta_aceptacion" class="form-control-file" accept=".pdf">
                                </div>
                            @endif

                            @if ($postulante->status == 1 && $postulante->pago_matricula == null)
                                <p style="font-weight: bold; font-size: 1.2em; color: #333;">
                                    Al realizar el pago usted está de acuerdo con los términos y condiciones establecidos por la institución.
                                </p>
                                <p style="font-weight: bold; font-size: 1.2em; color: #cc0000;">
                                    Tenga en cuenta que cualquier intento de falsificar o modificar el comprobante de pago puede resultar en sanciones según las políticas de la institución.
                                </p>
                                <p style="font-weight: bold; font-size: 1.2em; color: #333;">
                                    Los reembolsos están sujetos a los términos y condiciones establecidos por la institución.
                                </p>
                                <div class="mt-4">
                                    <p>Estas son las cuentas oficiales para realizar los pagos. Toda transacción debe hacerse a las siguientes cuentas:</p>
                                    <div class="text-center">
                                        <img src="{{ asset('images/numero_cuenta.jpeg') }}" alt="Cuentas oficiales" style="max-width: 50%; height: auto;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="comprobante_pago">Comprobante de Pago Matrícula:</label>
                                    <input type="file" id="comprobante_pago" name="pago_matricula" class="form-control-file" accept=".pdf">
                                </div>
                            @endif
                            @if((is_null($postulante->pago_matricula) && $postulante->status == true) || is_null($postulante->carta_aceptacion) || is_null($postulante->pdf_cedula) || is_null($postulante->pdf_papelvotacion) || is_null($postulante->pdf_titulouniversidad) || is_null($postulante->pdf_hojavida) || ($postulante->discapacidad == 'Sí' && is_null($postulante->pdf_conadis)))
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .header {
            text-align: center;
            margin-top: 10px;
        }
        .logo {
            width: 74px;
            height: 80px;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .seal {
            width: 100px;
            height: 103px;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .institute {
            font-size: 10pt;
        }
        .divider {
            width: 100%;
            height: 2px;
            background-color: #000;
            margin: 10px 0;
        }
        .custom-select-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .card-header {
        height: 120px; 
        padding: 20px; 
    }


</style>
@stop