@extends('adminlte::page')
@section('title', 'Dashboard Admin')
@section('content_header')
    <h1>Dashboard</h1>
@stop
@section('content')
@if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Usuarios</span>
                    <span class="info-box-number">{{ $totalUsuarios }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fa fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Docentes</span>
                    <span class="info-box-number">{{ $totalDocentes }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fa fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Secretarios</span>
                    <span class="info-box-number">{{ $totalSecretarios }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fa fa-user-graduate"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Alumnos</span>
                    <span class="info-box-number">{{ $totalAlumnos }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fa fa-graduation-cap"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Maestrías</span>
                    <span class="info-box-number">{{ $totalMaestrias }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Ajusta el tamaño y otras propiedades según tus necesidades -->
            <div class="card">
                <div class="card-body">
                    <canvas id="matriculadosChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <!-- Agrega más secciones para otras entidades según sea necesario -->
    </div>
</div>

@endsection

@section('css')
    <style>
        .hide-by-default {
            display: none;
        }
        .maestria-nombre {
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif; /* Ajusta el tamaño de fuente según tu preferencia */
        }

        /* Estilo para el ID de maestría */
        .maestria-id {
            font-weight: bold; /* Texto en negrita para el ID */
            margin-right: 3px; /* Espacio entre el ID y el nombre */
            cursor: pointer; /* Cambia el cursor al hacer hover sobre el ID */
        }
    </style>
@stop

@section('js')
    <script>

       // Datos para el gráfico de matriculados por maestría
        var matriculadosIDs = {!! $matriculadosPorMaestria->pluck('id') !!};
        var matriculadosNames = {!! $matriculadosPorMaestria->pluck('nombre') !!};
        var matriculadosValues = {!! $matriculadosPorMaestria->pluck('alumnos_count') !!};

        var matriculadosData = {
            labels: matriculadosIDs,
            datasets: [{
                label: 'Cantidad de Alumnos Matriculados',
                data: matriculadosValues,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)', // Color para la primera barra
                    'rgba(54, 162, 235, 0.2)', // Color para la segunda barra
                    'rgba(255, 206, 86, 0.2)', // Color para la tercera barra
                    'rgba(75, 192, 192, 0.2)', // Color para la cuarta barra
                    'rgba(153, 102, 255, 0.2)', // Color para la quinta barra
                    // Puedes agregar más colores según sea necesario
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                ],
                borderWidth: 1
            }]
        };

        var matriculadosOptions = {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = matriculadosNames[context.dataIndex] + ' (ID ' + matriculadosIDs[context.dataIndex] + '): ' + context.parsed.y + ' alumnos matriculados';
                            return label;
                        }
                    }
                }
            }
        };


        var matriculadosChart = new Chart(document.getElementById('matriculadosChart'), {
            type: 'bar',
            data: matriculadosData,
            options: matriculadosOptions
        });

    </script>
@stop