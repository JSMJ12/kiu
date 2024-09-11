<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas</title>
    <style>
        /* Estilos CSS */
        #fecha-actual {
            font-size: 12pt;
            text-align: right;
            margin-top: 10px;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-top: 10px;
        }
        .logo {
            width: 74px;
            height: 89px;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .seal {
            width: 74px;
            height: 73px;
            position: absolute;
            top: 30px;
            right: 10px;
        }
        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .institute {
            font-size: 10pt;
        }
        .coordinator {
            font-size: 10pt;
        }
        .divider {
            width: 100%;
            height: 2px;
            background-color: #000;
            margin: 10px 0;
        }
        .certificate-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .certificate-details {
            font-size: 12pt;
            text-align: justify;
            margin: 10px 0;
        }
        .student-info {
            font-size: 9pt;
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        .student-info th, .student-info td {
            border: 1px solid #666;
            padding: 3px; /* Reducir el espacio entre celdas */
            text-align: center; /* Centrar el contenido en las celdas */
        }
        .student-info th {
            background-color: #ccc;
        }
        .footer {
            font-size: 10pt;
            text-align: right;
            margin-top: 10px;
        }
        /* Ajustes para la tabla */
        .student-info td {
            text-align: center; /* Centrar el contenido en las celdas */
        }
        #qr-code {
            position: absolute;
            bottom: 30px;
            right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
            <img src="{{ public_path() . '/images/posg.jpg' }}" alt="University Seal" class="seal"><br>
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <span class="coordinator">COORDINACIÓN DE LA {{ $asignatura->maestria->nombre }}</span>
        </div>
        <div class="divider"></div>
        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>
        <div style="text-align: center; font-size: 12px; margin-bottom: 20px;">
            <strong>Información de la Asignatura</strong>
            <strong>Asignatura:</strong> {{ $asignatura->nombre }}
            @if($aula)
                <strong>Aula:</strong> {{ $aula->nombre }}
            @endif
            @if($paralelo)
                <strong>Paralelo:</strong> {{ $paralelo->nombre }}
            @endif
            <strong>Periodo:</strong> {{ $periodo_academico->nombre }}
            <strong>Cohorte:</strong> {{ $cohorte->nombre }}
            <strong>Docente:</strong> {{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }}
        </div>

        <table class="student-info" style="width: 100%; margin: 0 auto; font-size: 10pt; overflow-x: auto;">
            <thead>
                <tr>
                    <!-- Encabezados de la tabla -->
                    <th>Alumno</th>
                    <th>Ced./Pas</th>
                    <th>Actividades de Aprendizaje (2.5)</th>
                    <th>Prácticas de Apli. y Exp. (2.5)</th>
                    <th>Aprendizaje Autónomo (2.5)</th>
                    <th>Examen Final (2.5)</th>
                    <th>Recuperación</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnosMatriculados as $alumno)
                    <tr>
                        <!-- Datos de cada alumno -->
                        <td>
                            {{ $alumno->nombre1 }} 
                            {{ $alumno->nombre2 }} 
                            {{ $alumno->apellidop }} 
                            {{ $alumno->apellidom }}
                        </td>
                        <td>{{ $alumno->dni }}</td>
                        @foreach ($alumno->notas as $nota)
                            @if ($nota->asignatura_id == $asignatura->id && $nota->alumno_id == $alumno->id && $nota->docente_dni == $docente->dni)
                                <td>{{ $nota->nota_actividades }}</td>
                                <td>{{ $nota->nota_practicas }}</td>
                                <td>{{ $nota->nota_autonomo }}</td>
                                <td>{{ $nota->examen_final }}</td>
                                <td>{{ $nota->recuperacion }}</td>
                                <td>{{ $nota->total }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 20px;">
            <div style="text-align: left; width: 40%; float: left;">
                <!-- Espacio para la firma del docente (puedes agregar una línea o un espacio en blanco) -->
                <hr style="border: 1px solid #000; margin-top: 20px;">
                <div style="text-align: center;">
                    <strong>{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }}</strong>
                </div>
                <div style="text-align: center;">
                    <strong>{{ $docente->dni }}</strong>
                </div>
            </div>
            <div style="text-align: right; width: 40%; float: right;">
                <strong></strong><br>
                <!-- Espacio para el sello de la institución (puedes agregar una imagen o espacio en blanco) -->
            </div>
            <div style="clear: both;"></div>
        </div>
        <div id="qr-code">
            <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="Código QR">
        </div>
    </div>
</body>
</html>