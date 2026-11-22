<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/NotificacionModelo.php";
require_once __DIR__ . "/TiempoHelper.php";

function responder($datos, $codigoHttp = 200) {
    http_response_code($codigoHttp);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

// Recorta un texto largo para mostrarlo dentro de la notificacion
function recortar($texto, $max = 60) {
    if ($texto === null) {
        return null;
    }
    $texto = trim(preg_replace('/\s+/', ' ', $texto));
    return mb_strlen($texto) > $max ? mb_substr($texto, 0, $max) . "…" : $texto;
}

if (!isset($_SESSION["id_usuario"])) {
    responder(["ok" => false, "login" => true, "mensaje" => "Inicia sesión para ver tus notificaciones."], 401);
}

$idUsuario = (int) $_SESSION["id_usuario"];
$modelo    = new NotificacionModelo($conexion);
$accion    = $_REQUEST["accion"] ?? "";

if ($accion === "contar") {
    responder(["ok" => true, "total" => $modelo->ContarNoLeidas($idUsuario)]);
}

if ($accion === "listar") {
    $lista = [];

    foreach ($modelo->Listar($idUsuario) as $fila) {
        $lista[] = [
            "id"               => (int) $fila["id_notif"],
            "tipo"             => $fila["tipo"],
            "id_pub"           => $fila["id_pub"] !== null ? (int) $fila["id_pub"] : null,
            "id_origen"        => (int) $fila["id_origen"],
            "nombre"           => $fila["nombre"],
            "texto"            => recortar($fila["texto"]),
            "leida"            => (int) $fila["leida"] === 1,
            "hace"             => tiempoRelativo($fila["segundos"], $fila["fecha"]),
            "estado_solicitud" => $fila["estado_solicitud"] !== null ? (int) $fila["estado_solicitud"] : null
        ];
    }

    responder(["ok" => true, "notificaciones" => $lista]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $accion === "leer") {
    $modelo->MarcarLeida($idUsuario, (int) ($_POST["id_notif"] ?? 0));
    responder(["ok" => true, "total" => $modelo->ContarNoLeidas($idUsuario)]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $accion === "leer_todas") {
    $modelo->MarcarTodasLeidas($idUsuario);
    responder(["ok" => true, "total" => 0]);
}

responder(["ok" => false, "mensaje" => "Acción no válida."], 400);