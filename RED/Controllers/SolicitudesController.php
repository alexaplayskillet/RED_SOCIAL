<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/UsuarioModelo.php";

function responder($datos, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function formatearPersonas($filas) {
    $lista = [];
    foreach ($filas as $f) {
        $nombre = trim($f["nombre"]) !== "" ? $f["nombre"] : $f["tag"];
        $partes = preg_split("/\s+/", trim($nombre));
        $iniciales = count($partes) > 1
            ? mb_substr($partes[0], 0, 1) . mb_substr($partes[1], 0, 1)
            : mb_substr($nombre, 0, 2);

        $lista[] = [
            "id"        => (int) $f["id_usuario"],
            "nombre"    => $nombre,
            "tag"       => $f["tag"],
            "iniciales" => mb_strtoupper($iniciales),
            "tieneFoto" => (int) $f["tiene_foto"] === 1,
            "mutuos"    => (int) $f["mutuos"]
        ];
    }
    return $lista;
}

if (!isset($_SESSION["id_usuario"])) {
    responder(["ok" => false, "mensaje" => "Sesión no iniciada."], 401);
}

$idUsuario = (int) $_SESSION["id_usuario"];
$modelo = new UsuarioModelo($conexion);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $lista = $_GET["lista"] ?? "";

    if ($lista === "sugerencias") {
        $limite = isset($_GET["limite"]) ? (int) $_GET["limite"] : 0;
        if ($limite < 0 || $limite > 50) {
            $limite = 50;
        }
        responder(["ok" => true, "personas" => formatearPersonas($modelo->obtenerSugerencias($idUsuario, $limite))]);
    }

    if ($lista === "recibidas") {
        responder(["ok" => true, "personas" => formatearPersonas($modelo->obtenerSolicitudesRecibidas($idUsuario))]);
    }

    responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $idOtro = (int) ($_POST["id_usuario"] ?? 0);

    $acciones = ["enviar" => "ENVIAR", "aceptar" => "ACEPTAR", "rechazar" => "RECHAZAR"];

    if (!isset($acciones[$accion]) || $idOtro <= 0) {
        responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
    }

    $codigo = $modelo->responderSolicitud($acciones[$accion], $idUsuario, $idOtro);

    $mensajes = [
        0 => ["enviar" => "Solicitud enviada.", "aceptar" => "Ahora son amigos.", "rechazar" => "Solicitud rechazada."],
        1 => "Ese usuario no existe.",
        2 => "No puedes enviarte una solicitud a ti mismo.",
        3 => "Ya son amigos.",
        4 => "Ya le enviaste una solicitud a esta persona.",
        5 => "Esta persona ya te envió una solicitud. Revísala en Solicitudes de amistad.",
        6 => "Esa solicitud ya no está disponible.",
        7 => "Ocurrió un error. Intenta de nuevo."
    ];

    if ($codigo === 0) {
        responder(["ok" => true, "mensaje" => $mensajes[0][$accion]]);
    }
    responder(["ok" => false, "mensaje" => $mensajes[$codigo] ?? $mensajes[7]], 400);
}

responder(["ok" => false, "mensaje" => "Método no permitido."], 405);