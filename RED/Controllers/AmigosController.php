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

if (!isset($_SESSION["id_usuario"])) {
    responder(["ok" => false, "mensaje" => "Sesión no iniciada."], 401);
}

$idUsuario = (int) $_SESSION["id_usuario"];
$modelo = new UsuarioModelo($conexion);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $busqueda = trim($_GET["q"] ?? "");
    $limite = isset($_GET["limite"]) ? (int) $_GET["limite"] : 0;
    if (mb_strlen($busqueda) > 100) {
        $busqueda = mb_substr($busqueda, 0, 100);
    }

    $amigos = $modelo->listarAmigos($idUsuario, $busqueda, $limite);
    $total = $modelo->contarAmigos($idUsuario);

    $lista = [];
    foreach ($amigos as $a) {
        $nombre = trim($a["nombre"]) !== "" ? $a["nombre"] : $a["tag"];
        $partes = preg_split("/\s+/", trim($nombre));
        $iniciales = count($partes) > 1
            ? mb_substr($partes[0], 0, 1) . mb_substr($partes[1], 0, 1)
            : mb_substr($nombre, 0, 2);

        $lista[] = [
            "id"        => (int) $a["id_usuario"],
            "nombre"    => $nombre,
            "tag"       => $a["tag"],
            "iniciales" => mb_strtoupper($iniciales),
            "tieneFoto" => (int) $a["tiene_foto"] === 1,
            "mutuos"    => (int) $a["mutuos"]
        ];
    }

    responder(["ok" => true, "total" => $total, "amigos" => $lista]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $idAmigo = (int) ($_POST["id_amigo"] ?? 0);

    if ($accion !== "eliminar" || $idAmigo <= 0) {
        responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
    }

    $codigo = $modelo->eliminarAmigo($idUsuario, $idAmigo);

    if ($codigo === 0) {
        responder(["ok" => true, "mensaje" => "Amigo eliminado."]);
    }
    if ($codigo === 1) {
        responder(["ok" => false, "mensaje" => "Esa persona ya no está en tu lista de amigos."], 404);
    }
    responder(["ok" => false, "mensaje" => "No se pudo eliminar al amigo."], 400);
}

responder(["ok" => false, "mensaje" => "Método no permitido."], 405);