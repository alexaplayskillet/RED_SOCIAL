<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/MensajeModelo.php";

function responder($datos, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function iniciales($nombre, $tag) {
    $nombre = trim($nombre) !== "" ? $nombre : $tag;
    $partes = preg_split("/\s+/", trim($nombre));
    $ini = count($partes) > 1
        ? mb_substr($partes[0], 0, 1) . mb_substr($partes[1], 0, 1)
        : mb_substr($nombre, 0, 2);
    return mb_strtoupper($ini);
}

function nombreMostrado($fila) {
    return trim($fila["nombre"]) !== "" ? $fila["nombre"] : $fila["tag"];
}

// Mensaje de error y codigo HTTP segun el codigo que regresa el procedimiento
function errorMensaje($codigo) {
    $mensajes = [
        1 => ["Ese usuario no existe.", 404],
        2 => ["Acción no válida.", 400],
        3 => ["Solo puedes chatear con tus amigos.", 403],
        4 => ["Escribe un mensaje.", 400],
        5 => ["No puedes chatear contigo mismo.", 400],
        6 => ["El mensaje no puede pasar de 255 caracteres.", 400],
        7 => ["Ocurrió un error. Intenta de nuevo.", 500]
    ];
    $dato = $mensajes[$codigo] ?? $mensajes[7];
    responder(["ok" => false, "mensaje" => $dato[0]], $dato[1]);
}

if (!isset($_SESSION["id_usuario"])) {
    responder(["ok" => false, "mensaje" => "Sesión no iniciada."], 401);
}

$idUsuario = (int) $_SESSION["id_usuario"];
$modelo = new MensajeModelo($conexion);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $accion = $_GET["accion"] ?? "";
    $idOtro = (int) ($_GET["id"] ?? 0);

    if ($accion === "chats") {
        $chats = [];
        foreach ($modelo->listarChats($idUsuario) as $c) {
            $chats[] = [
                "idChat"      => (int) $c["id_chat"],
                "id"          => (int) $c["id_usuario"],
                "nombre"      => nombreMostrado($c),
                "tag"         => $c["tag"],
                "iniciales"   => iniciales($c["nombre"], $c["tag"]),
                "tieneFoto"   => (int) $c["tiene_foto"] === 1,
                "ultimo"      => $c["ultimo"],
                "ultimaFecha" => $c["ultima_fecha"],
                "ultimoMio"   => $c["ultimo_emisor"] !== null && (int) $c["ultimo_emisor"] === $idUsuario
            ];
        }
        responder(["ok" => true, "chats" => $chats]);
    }

    if ($idOtro <= 0) {
        responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
    }

    if ($accion === "abrir") {
        $c = $modelo->abrirChat($idUsuario, $idOtro);
        if ((int) $c["codigo"] !== 0) {
            errorMensaje((int) $c["codigo"]);
        }
        responder([
            "ok"   => true,
            "chat" => [
                "idChat"        => (int) $c["id_chat"],
                "id"            => (int) $c["id_usuario"],
                "nombre"        => nombreMostrado($c),
                "tag"           => $c["tag"],
                "iniciales"     => iniciales($c["nombre"], $c["tag"]),
                "tieneFoto"     => (int) $c["tiene_foto"] === 1,
                "puedeEscribir" => (int) $c["puede_escribir"] === 1
            ]
        ]);
    }

    if ($accion === "mensajes") {
        $ultimo = max(0, (int) ($_GET["ultimo"] ?? 0));
        $filas = $modelo->listarMensajes($idUsuario, $idOtro, $ultimo);

        if (is_int($filas)) {
            errorMensaje($filas);
        }

        $mensajes = [];
        foreach ($filas as $m) {
            $mensajes[] = [
                "id"    => (int) $m["id_mensaje"],
                "mio"   => (int) $m["mio"] === 1,
                "texto" => $m["contenido"],
                "fecha" => $m["fecha_envio"]
            ];
        }
        responder(["ok" => true, "mensajes" => $mensajes]);
    }

    responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $idOtro = (int) ($_POST["id"] ?? 0);
    $texto = trim($_POST["texto"] ?? "");

    if ($accion !== "enviar" || $idOtro <= 0) {
        responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
    }

    $r = $modelo->enviarMensaje($idUsuario, $idOtro, $texto);
    if ((int) $r["codigo"] !== 0) {
        errorMensaje((int) $r["codigo"]);
    }

    responder([
        "ok"      => true,
        "mensaje" => [
            "id"    => (int) $r["id_mensaje"],
            "mio"   => true,
            "texto" => $texto,
            "fecha" => $r["fecha_envio"]
        ]
    ]);
}

responder(["ok" => false, "mensaje" => "Método no permitido."], 405);
