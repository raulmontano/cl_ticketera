<?php

use Carbon\Carbon;

function createSelectArray($array, $withNull = false, $nullOption = '--')
{
    if (! $array) {
        return [];
    }
    $values = $array->pluck('name', 'id')->toArray();
    if ($withNull) {
        return ['' => $nullOption] + $values;
    }

    return $values;
}

function nameOrDash($object)
{
    return ($object && $object->name) ? $object->name : '--';
}

function icon($icon)
{
    return FA::icon($icon);
}

function gravatar($email, $size = 30)
{
    $gravatarURL  = gravatarUrl($email, $size);

    return '<img id = '.$email.''.$size.' class="gravatar" src="'.$gravatarURL.'" width="'.$size.'">';
}

function gravatarUrl($email, $size)
{
    $email = md5(strtolower(trim($email)));
    //$gravatarURL = "https://www.gravatar.com/avatar/" . $email."?s=".$size."&d=mm";
    $defaultImage = urlencode('https://raw.githubusercontent.com/BadChoice/handesk/master/public/images/default-avatar.png');

    return 'https://www.gravatar.com/avatar/'.$email.'?s='.$size."&default={$defaultImage}";
}

function toTime($minutes)
{
    $minutes_per_day = (Carbon::HOURS_PER_DAY * Carbon::MINUTES_PER_HOUR);
    $days            = floor($minutes / ($minutes_per_day));
    $hours           = floor(($minutes - $days * ($minutes_per_day)) / Carbon::MINUTES_PER_HOUR);
    $mins            = (int) ($minutes - ($days * ($minutes_per_day)) - ($hours * 60));

    return "{$days} Days {$hours} Hours {$mins} Mins";
}

function toPercentage($value, $inverse = false)
{
    return  ($inverse ? 1 - $value : $value) * 100;
}

//date_default_timezone_set('America/Santiago');

function calcularFechaSolucion($f_asignacion, $id_solicitud = null)
{

    // Verificar si $f_asignacion es null
    if ($f_asignacion === null) {
        return "<span style='color:#ccc'>No disponible</span>";
    }

    switch (date('N', strtotime($f_asignacion))) {
        case 4://DIA JUEVES
            $f_solucion = diaJueves($f_asignacion, $id_solicitud);
            break;

        case 5://DIA VIERNES
            $f_solucion = diaViernes($f_asignacion, $id_solicitud);
            break;

        default:
            $f_solucion = diasLunesMiercoles($f_asignacion, $id_solicitud);
            break;
    }

    return $f_solucion !== null ? date('Y-m-d H:i:s', (int)$f_solucion) : null;
}

function calcularDia($f_asignacion, $dias)
{
    $f_solucion = strtotime($f_asignacion . ' +' . $dias . ' days');

    if ($f_solucion !== false) {
        // Realizar la consulta a la base de datos para verificar feriados
        // $feriadoInfo = obtenerInformacionFeriado($f_solucion);

        // Lógica para manejar feriados y feriados continuos
        // ...

        return $f_solucion;
    } else {
        // Manejar el caso en que strtotime falla
        return null;
    }
}

function diaJueves($f_asignacion, $id_solicitud)
{
    return calcularDia($f_asignacion, 3);//cambie de 1 a 3
}

function diasLunesMiercoles($f_asignacion, $id_solicitud)
{
    return calcularDia($f_asignacion, 3);//cambie de 1 a 3
}

function diaViernes($f_asignacion, $id_solicitud)
{
    return calcularDia($f_asignacion, 4);//cambie de 3 a 6
}

function DiferenciaTiempoTranscurrido($f_asignacion, $pausedTime = 0, $f_solucionado = false)
{
    //date_default_timezone_set('America/Santiago');

    if ($f_solucionado) {
        $tiempoActual = $f_solucionado;
    } else {
        $tiempoActual = date('Y-m-d H:i:s');
    }
    // Calcular la diferencia en segundos entre f_solucionado y la fecha actual

    //echo "Tiempo actual: $tiempoActual<br>";
    //$diferencia_segundos = strtotime(date('Y-m-d H:i:s')) - strtotime($f_asignacion);
    $diferencia_segundos = strtotime($tiempoActual) - strtotime($f_asignacion) - $pausedTime;
    //echo "Diferencia en segundos: $diferencia_segundos<br>";

    // Calcular días, horas y minutos
    $dias = floor($diferencia_segundos / (60 * 60 * 24));
    $horas = floor(($diferencia_segundos % (60 * 60 * 24)) / (60 * 60));
    $minutos = floor(($diferencia_segundos % (60 * 60)) / 60);

    // Imprimir valores para comprobación
    //echo "Días: $dias, Horas: $horas, Minutos: $minutos<br>";

    // Crear la cadena de resultado
    $resultado = '';
    if ($dias > 0) {
        $resultado .= $dias . 'd, ';
    }
    if ($horas > 0 || $dias > 0) {
        $resultado .= $horas . 'h, ';
    }
    $resultado .= $minutos . 'm';

    // Aplicar colores según la lógica de días
    $color = '';
    if ($dias< 2) {
        $color = '#2ECC71';
    } elseif ($dias > 1 && $dias <= 2) {
        $color = '#F4D03F';
    } elseif ($dias > 2) {
        $color = '#E74C3C';
    }

    // Devolver el resultado junto con el color
    return '<span style="color: ' . $color . ';">' . $resultado . '</span>';
}
