<?php
session_start();

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/PublicacionModelo.php";

// Entrega la foto o el video de una publicacion: MediaController.php?id=5
// Las publicaciones son publicas, asi que no hace falta iniciar sesion para verlas
$idUsuario = (int) ($_SESSION["id_usuario"] ?? 0);
session_write_close();

$idPub  = (int) ($_GET["id"] ?? 0);
$modelo = new PublicacionModelo($conexion);
$media  = $modelo->ObtenerMedia($idUsuario, $idPub);

if ($media === null || $media["media"] === null) {
    http_response_code(404);
    exit;
}

$datos = $media["media"];
$tipo  = $media["tipo"];

if (empty($tipo) || $tipo === "application/octet-stream") {
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->buffer($datos) ?: "application/octet-stream";
}

$total = strlen($datos);
$etag  = '"' . md5($datos) . '"';

header("Content-Type: " . $tipo);
header("Accept-Ranges: bytes");
header("Cache-Control: private, no-cache");
header("ETag: " . $etag);

// Si el navegador ya tiene esta misma version guardada, no se vuelve a mandar
if (trim($_SERVER["HTTP_IF_NONE_MATCH"] ?? "") === $etag) {
    http_response_code(304);
    exit;
}

// Los videos piden el archivo por partes (Range) para poder adelantar y regresar
$inicio = 0;
$fin    = $total - 1;

if (isset($_SERVER["HTTP_RANGE"]) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER["HTTP_RANGE"], $partes)) {
    if ($partes[1] === "" && $partes[2] !== "") {
        $inicio = max(0, $total - (int) $partes[2]);
    } else {
        $inicio = (int) $partes[1];
        if ($partes[2] !== "") {
            $fin = min((int) $partes[2], $total - 1);
        }
    }

    if ($inicio > $fin || $inicio >= $total) {
        http_response_code(416);
        header("Content-Range: bytes */" . $total);
        exit;
    }

    http_response_code(206);
    header("Content-Range: bytes " . $inicio . "-" . $fin . "/" . $total);
}

header("Content-Length: " . ($fin - $inicio + 1));
echo substr($datos, $inicio, $fin - $inicio + 1);