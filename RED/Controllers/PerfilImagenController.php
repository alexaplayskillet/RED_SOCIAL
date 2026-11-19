<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

function responder(bool $ok, string $mensaje): void {
    echo json_encode(["ok" => $ok, "mensaje" => $mensaje]);
    exit;
}

require_once __DIR__ . "/../model/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    responder(false, "Sesión no iniciada");
}

$idUsuario = (int) $_SESSION["id_usuario"];
$accion    = $_POST["accion"] ?? "";
$tipo      = strtoupper($_POST["tipo"] ?? "");

if (!in_array($tipo, ["PORTADA", "FOTO"], true)) {
    responder(false, "Tipo de imagen no válido");
}

$maxBytes = 5 * 1024 * 1024;
$tiposOk  = ["image/jpeg", "image/png", "image/webp"];

if ($accion === "guardar") {

    if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
        responder(false, "No se recibió la imagen (revisa upload_max_filesize en php.ini)");
    }

    $archivo = $_FILES["imagen"];

    if ($archivo["size"] > $maxBytes) {
        responder(false, "La imagen pesa más de 5 MB");
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo["tmp_name"]);
    if (!in_array($mime, $tiposOk, true)) {
        responder(false, "Formato no permitido (usa JPG, PNG o WEBP)");
    }

    $datos     = file_get_contents($archivo["tmp_name"]);
    $accionSql = "GUARDAR";
    $nulo      = null;

    $stmt = $conexion->prepare("CALL gestionar_imagen_perfil(?, ?, ?, ?)");
    $stmt->bind_param("sisb", $accionSql, $idUsuario, $tipo, $nulo);
    $stmt->send_long_data(3, $datos);

} elseif ($accion === "eliminar") {

    $accionSql = "ELIMINAR";
    $stmt = $conexion->prepare("CALL gestionar_imagen_perfil(?, ?, ?, NULL)");
    $stmt->bind_param("sis", $accionSql, $idUsuario, $tipo);

} else {
    responder(false, "Acción no válida");
}

$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

$mensajes = [
    0 => "OK",
    1 => "Usuario no encontrado",
    2 => "Acción no válida",
    3 => "No se envió ninguna imagen",
    4 => "Tipo de imagen no válido"
];

$codigo = (int) ($fila["codigo"] ?? -1);
responder($codigo === 0, $mensajes[$codigo] ?? "Error desconocido");