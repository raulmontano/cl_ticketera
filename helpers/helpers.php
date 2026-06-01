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


function calcularFechaSolucion($f_asignacion)
{
    if (empty($f_asignacion)) {
        return null;
    }

    $timestamp = is_numeric($f_asignacion)
        ? (int) $f_asignacion
        : strtotime($f_asignacion);

    if ($timestamp === false) {
        return null;
    }

    $fechaCambio = strtotime('2026-06-01 00:00:00');

    if ($timestamp < $fechaCambio) {
        $diasHabiles = 2;
    } else {
        $diasHabiles = (int) env('COMMITMENT_DAYS', 3);
    }

    $fechaCompromiso = $timestamp;
    $diasAgregados = 0;

    while ($diasAgregados < $diasHabiles) {

        $fechaCompromiso = strtotime('+1 day', $fechaCompromiso);

        $diaSemana = (int) date('N', $fechaCompromiso);

        // Lunes(1) a Viernes(5)
        if ($diaSemana <= 5) {
            $diasAgregados++;
        }
    }

    return date('Y-m-d H:i:s', $fechaCompromiso);
}


function obtenerSemaforoCompromiso($fechaCompromiso, $fechaRealSolucion = null)
{
    if (empty($fechaCompromiso)) {
        return null;
    }

    $fechaCompromisoTs = is_numeric($fechaCompromiso)
        ? (int) $fechaCompromiso
        : strtotime($fechaCompromiso);

    if ($fechaCompromisoTs === false) {
        return null;
    }

    // Si ya existe una fecha de solución,
    // evaluamos contra ella; de lo contrario contra hoy.
    $fechaReferenciaTs = !empty($fechaRealSolucion)
        ? (is_numeric($fechaRealSolucion)
            ? (int) $fechaRealSolucion
            : strtotime($fechaRealSolucion))
        : time();

    if ($fechaReferenciaTs === false) {
        return null;
    }

    $diaCompromiso = date('Y-m-d', $fechaCompromisoTs);
    $diaReferencia = date('Y-m-d', $fechaReferenciaTs);

    if ($diaReferencia < $diaCompromiso) {
        return '#2ECC71';
    }

    if ($diaReferencia === $diaCompromiso) {
        return '#F4D03F';
    }

    return '#E74C3C';
}

function DiferenciaTiempoTranscurrido($f_asignacion, $pausedTime = 0, $f_solucionado = false)
{
    //date_default_timezone_set('America/Santiago');

    if ($f_solucionado) {
        $tiempoActual = $f_solucionado;
    } else {
        $tiempoActual = date('Y-m-d H:i:s');
        $hoursActual = date('H');
        $carbonActual = Carbon::parse($tiempoActual);
    }

    $weekendDays = $f_asignacion->diffInDaysFiltered(function (Carbon $date) {
        return !$date->isWeekday();
    }, $tiempoActual);

    //descontar tiempo parcial en caso de consultar el ticket en sabado o domingo
    if (isset($carbonActual) && ($carbonActual->dayOfWeek == Carbon::SATURDAY || $carbonActual->dayOfWeek == Carbon::SUNDAY)) {
        $pausedTime += $hoursActual;
    }

    $pausedTime += $weekendDays * 24 * 60 * 60;
    //dump($weekendDays, $pausedTime);
    //dump($days, $f_asignacion->format('Y-m-d H:i:s'), $tiempoActual, $pausedTime);

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
