<?php
session_start();

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/UsuarioModelo.php";

if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    exit;
}

$idUsuario = (int) $_SESSION["id_usuario"];
$modelo = new UsuarioModelo($conexion);

if (isset($_GET["id"])) {
    $imagen = $modelo->obtenerImagenGaleria($idUsuario, (int) $_GET["id"]);

    if ($imagen === null) {
        http_response_code(404);
        exit;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($imagen);

    header("Content-Type: " . $mime);
    header("Content-Length: " . strlen($imagen));
    header("Cache-Control: private, max-age=3600");
    echo $imagen;
    exit;
}

header("Content-Type: application/json; charset=utf-8");

function responder($datos, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $idPub = (int) ($_POST["id"] ?? 0);

    if ($idPub <= 0 || !in_array($accion, ["editar", "eliminar"], true)) {
        responder(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
    }

    if ($accion === "editar") {
        $texto = trim($_POST["texto"] ?? "");
        if (mb_strlen($texto) > 255) {
            responder(["ok" => false, "mensaje" => "El texto no puede pasar de 255 caracteres."], 400);
        }
        $codigo = $modelo->editarFoto($idUsuario, $idPub, $texto);
        $exito = "Foto actualizada.";
    } else {
        $codigo = $modelo->eliminarFoto($idUsuario, $idPub);
        $exito = "Foto eliminada.";
    }

    if ($codigo === 0) {
        responder(["ok" => true, "mensaje" => $exito, "texto" => $accion === "editar" ? $texto : null]);
    }
    if ($codigo === 1) {
        responder(["ok" => false, "mensaje" => "Esa foto ya no existe."], 404);
    }
    if ($codigo === 3) {
        responder(["ok" => false, "mensaje" => "El texto no puede pasar de 255 caracteres."], 400);
    }
    responder(["ok" => false, "mensaje" => "Ocurrió un error. Intenta de nuevo."], 500);
}

$limite = isset($_GET["limite"]) ? (int) $_GET["limite"] : 0;
if ($limite < 0) {
    $limite = 0;
}

$fotos = [];
foreach ($modelo->listarGaleria($idUsuario, $limite) as $f) {
    $fotos[] = [
        "id"    => (int) $f["id_pub"],
        "texto" => (string) $f["texto"],
        "fecha" => $f["fecha_pub"]
    ];
}

echo json_encode(["ok" => true, "fotos" => $fotos], JSON_UNESCAPED_UNICODE);