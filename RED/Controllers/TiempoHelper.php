<?php
function tiempoRelativo($segundos, $respaldo = "") {
    if ($segundos === null || $segundos === "") {
        return $respaldo;
    }

    $s = max(0, (int) $segundos);

    if ($s < 60) {
        return "hace un momento";
    }

    $minutos = intdiv($s, 60);
    if ($minutos < 60) {
        return "hace " . $minutos . " min";
    }

    $horas = intdiv($minutos, 60);
    if ($horas < 24) {
        return $horas === 1 ? "hace 1 hora" : "hace " . $horas . " horas";
    }

    $dias = intdiv($horas, 24);
    if ($dias < 7) {
        return $dias === 1 ? "hace un día" : "hace " . $dias . " días";
    }

    if ($dias < 30) {
        $semanas = intdiv($dias, 7);
        return $semanas === 1 ? "hace una semana" : "hace " . $semanas . " semanas";
    }

    if ($dias < 365) {
        $meses = intdiv($dias, 30);
        return $meses === 1 ? "hace un mes" : "hace " . $meses . " meses";
    }

    $anios = intdiv($dias, 365);
    return $anios === 1 ? "hace un año" : "hace " . $anios . " años";
}
?>