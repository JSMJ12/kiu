@extends('adminlte::page')
@section('title', 'Pagos')

@section('content_header')
    <h1>Pagos Realizados y Pendientes</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <!-- Gráfico de Pagos Realizados -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3>Pagos Realizados</h3>
                    <div class="btn-group" role="group" aria-label="Tipos de análisis">
                        <button type="button" class="btn btn-secondary" id="analisisDia">Día</button>
                        <button type="button" class="btn btn-secondary" id="analisisMes">Mes</button>
                        <button type="button" class="btn btn-secondary" id="analisisAnio">Año</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="pagosChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Cantidad de Pagos Realizados -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3>Cantidad de Pagos Realizados</h3>
                    <div class="btn-group" role="group" aria-label="Tipos de análisis">
                        <button type="button" class="btn btn-secondary" id="cantidadDia">Día</button>
                        <button type="button" class="btn btn-secondary" id="cantidadMes">Mes</button>
                        <button type="button" class="btn btn-secondary" id="cantidadAnio">Año</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="cantidadPagosChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla de pagos -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Historial de Pagos</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="pagosTable">
                        <thead>
                            <tr>
                                <th>Cedula/Pasaporte</th>
                                <th>Monto</th>
                                <th>Fecha de Pago</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todosPagos as $pago)
                            <tr>
                                <td>{{ $pago->dni }}</td>
                                <td>${{ number_format($pago->monto, 2) }}</td>
                                <td>{{ $pago->fecha_pago }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $pago->archivo_comprobante) }}" target="_blank" class="btn btn-info btn-sm" title="Ver Comprobante">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                </td>
                                <td>
                                    @if (!$pago->verificado)
                                        <form action="{{ route('pagos.verificar', $pago->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">Verificado</button>
                                        </form>
                                    @else
                                        <span class="badge badge-success">Verificado</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#pagosTable').DataTable({
            paging: true, // Asegúrate de que la paginación esté habilitada
            lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100], 
            pageLength: {{ $perPage }},
            responsive: true, 
            colReorder: true,
            keys: true,
            autoFill: true, 
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
        // Configuración inicial del gráfico de pagos realizados
        var ctxPagos = document.getElementById('pagosChart').getContext('2d');
        var pagosChart = new Chart(ctxPagos, {
            type: 'line',
            data: {
                labels: [], // Etiquetas se actualizarán dinámicamente
                datasets: [{
                    label: 'Monto Total Pagado',
                    data: [], // Datos se actualizarán dinámicamente
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Configuración inicial del gráfico de cantidad de pagos realizados
        var ctxCantidad = document.getElementById('cantidadPagosChart').getContext('2d');
        var cantidadPagosChart = new Chart(ctxCantidad, {
            type: 'line',
            data: {
                labels: [], // Etiquetas se actualizarán dinámicamente
                datasets: [{
                    label: 'Cantidad de Pagos Realizados',
                    data: [], // Datos se actualizarán dinámicamente
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Función para actualizar los gráficos
        function updateChart(chart, labels, data) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.update();
        }

        // Función para actualizar el gráfico de pagos realizados
        function updatePagosChart(type) {
            var labels = [];
            var data = [];

            if (type === 'dia') {
                labels = @json($pagosPorHora->keys());
                data = @json($pagosPorHora->values());
            } else if (type === 'mes') {
                labels = @json($pagosPorDia->keys());
                data = @json($pagosPorDia->values());
            } else if (type === 'anio') {
                labels = @json($pagosPorMes->keys());
                data = @json($pagosPorMes->values());
            }

            updateChart(pagosChart, labels, data);
        }

        // Función para actualizar el gráfico de cantidad de pagos realizados
        function updateCantidadChart(type) {
            var labels = [];
            var data = [];

            if (type === 'dia') {
                labels = @json($cantidadPorHora->keys());
                data = @json($cantidadPorHora->values());
            } else if (type === 'mes') {
                labels = @json($cantidadPorDia->keys());
                data = @json($cantidadPorDia->values());
            } else if (type === 'anio') {
                labels = @json($cantidadPorMes->keys());
                data = @json($cantidadPorMes->values());
            }

            updateChart(cantidadPagosChart, labels, data);
        }

        // Manejar los clics de los botones para el gráfico de pagos realizados
        $('#analisisDia').click(function() {
            updatePagosChart('dia');
        });
        $('#analisisMes').click(function() {
            updatePagosChart('mes');
        });
        $('#analisisAnio').click(function() {
            updatePagosChart('anio');
        });

        // Manejar los clics de los botones para el gráfico de cantidad de pagos realizados
        $('#cantidadDia').click(function() {
            updateCantidadChart('dia');
        });
        $('#cantidadMes').click(function() {
            updateCantidadChart('mes');
        });
        $('#cantidadAnio').click(function() {
            updateCantidadChart('anio');
        });
    });
</script>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<style>
    .card-header {
        background-color: #3b8b54;
        color: white;
    }
    .card-body {
        padding: 20px;
    }
    table {
        width: 100%;
    }
    .table th, .table td {
        text-align: center;
    }
</style>
@stop
