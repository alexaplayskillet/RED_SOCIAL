<?php
session_start();

require_once __DIR__ . "/../model/conexion.php";

$tipo    = strtoupper($_GET["tipo"] ?? "");
$idAmigo = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

// Sin sesion solo se puede ver la foto de perfil de otro usuario (por ejemplo en los comentarios)
if (!isset($_SESSION["id_usuario"]) && !($tipo === "FOTO" && $idAmigo > 0)) {
    http_response_code(401);
    exit;
}

$idUsuario = (int) ($_SESSION["id_usuario"] ?? 0);

if (!in_array($tipo, ["PORTADA", "FOTO"], true)) {
    http_response_code(400);
    exit;
}

if ($idAmigo > 0 && $idAmigo !== $idUsuario) {
    if ($tipo !== "FOTO") {
        http_response_code(400);
        exit;
    }
    $accion = "FOTO";
    $stmt = $conexion->prepare("CALL gestionar_amigos(?, ?, ?, NULL, NULL)");
    $stmt->bind_param("sii", $accion, $idUsuario, $idAmigo);
} else {
    $accion = "OBTENER";
    $stmt = $conexion->prepare("CALL gestionar_imagen_perfil(?, ?, ?, NULL)");
    $stmt->bind_param("sis", $accion, $idUsuario, $tipo);
}
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fila || (int) $fila["codigo"] !== 0 || $fila["imagen"] === null) {
    http_response_code(404);
    exit;
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($fila["imagen"]);

header("Content-Type: " . $mime);
header("Content-Length: " . strlen($fila["imagen"]));
header("Cache-Control: private, no-cache");
echo $fila["imagen"];