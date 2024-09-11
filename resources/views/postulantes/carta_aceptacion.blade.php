<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carta de Aceptación</title>
    <style>
        #fecha-actual {
            font-size: 12pt;
            text-align: right;
            margin-top: 10px;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
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
        .firma {
            text-align: center;
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
            font-size: 10pt;
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        .student-info th, .student-info td {
            border: 1px solid #666;
            padding: 5px;
        }
        .student-info th {
            background-color: #ccc;
        }
        .footer {
            font-size: 10pt;
            text-align: right;
            margin-top: 10px;
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
            <span class="coordinator">COORDINACIÓN DE LA {{ $postulante->maestria->nombre }}</span>
        </div>
        <div class="divider"></div>
        @if ($postulante->maestria->cohorte)
            @php
                $fecha_actual = now();
                
                // Filtramos los cohortes cuya fecha de inicio esté dentro de los próximos 5 días
                $cohorte_encontrado = $postulante->maestria->cohorte->filter(function ($cohorte) use ($fecha_actual) {
                    return $cohorte->fecha_inicio >= $fecha_actual && $cohorte->fecha_inicio <= $fecha_actual->copy()->addDays(5);
                })->first();
                
                // Si no hay cohortes dentro de los próximos 5 días
                if (!$cohorte_encontrado) {
                    // Buscamos el siguiente cohorte más cercano en el tiempo que esté al menos a 10 días de distancia
                    $cohorte_encontrado = $postulante->maestria->cohorte->filter(function ($cohorte) use ($fecha_actual) {
                        return $cohorte->fecha_inicio > $fecha_actual->copy()->addDays(10);
                    })->sortBy('fecha_inicio')->first();
                }
            @endphp
        @endif
        <div id="fecha-actual">
            Jipijapa, {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </div>        
        <div class="certificate-details">
            <p>Señores<br>Instituto de Posgrado UNESUM<br>Presente.-</p>
            <p>De mi consideración</p>
            <p>Quien suscribe {{ $postulante->nombre1 }} {{ $postulante->nombre2 }} {{ $postulante->apellidop }} {{ $postulante->apellidom }} con cédula de identidad No. {{ $postulante->dni }} de profesión {{ $postulante->titulo_profesional }} a través de la presente comunico que ACEPTO el cupo al Programa de Maestría en {{ $postulante->maestria->nombre }} - COHORTE {{ $cohorte_encontrado ? $cohorte_encontrado->nombre : 'N/A' }} a impartirse en el Instituto de Posgrado de la Universidad Estatal del Sur de Manabí.</p>
            <p>Sin otro particular reitero mis agradecimientos.</p>
            <p>Atentamente,</p>
        </div>
        <div class="firma">
            <br>
            <br>
            <br>
            <p>____________________________<br>{{ $postulante->nombre1 }} {{ $postulante->nombre2 }} {{ $postulante->apellidop }} {{ $postulante->apellidom }}<br>CI: {{ $postulante->dni }}</p>
        </div>
        
        
    </div>
</body>
</html>
