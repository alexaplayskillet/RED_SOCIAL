<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

function responder(bool $ok, string $mensaje): void {
    echo json_encode(["ok" => $ok, "mensaje" => $mensaje]);
    exit;
}

if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    responder(false, "Sesión no iniciada");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Método no permitido");
}

$nombre   = trim($_POST["nombre"] ?? "");
$tag      = trim($_POST["tag"] ?? "");
$correo   = trim($_POST["correo"] ?? "");
$fechaNac = $_POST["fecha"] ?? "";

if ($nombre === "" || $tag === "" || $correo === "" || $fechaNac === "") {
    responder(false, "Todos los campos son obligatorios");
}

if (mb_strlen($nombre) > 100) {
    responder(false, "El nombre es demasiado largo (máximo 100 caracteres)");
}

if (!preg_match('/^[a-zA-Z0-9_-]{4,16}$/', $tag)) {
    responder(false, "El usuario debe tener de 4 a 16 caracteres: letras, números, guiones o guiones bajos");
}

if (mb_strlen($correo) > 100 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responder(false, "El correo no es válido");
}

$f = DateTime::createFromFormat("Y-m-d", $fechaNac);
if (!$f || $f->format("Y-m-d") !== $fechaNac || $f > new DateTime()) {
    responder(false, "La fecha de nacimiento no es válida");
}

try {
    require_once __DIR__ . "/../model/conexion.php";
    require_once __DIR__ . "/../model/UsuarioModelo.php";

    $modelo = new UsuarioModelo($conexion);
    $resultado = $modelo->editarPerfil((int) $_SESSION["id_usuario"], $nombre, $tag, $correo, $fechaNac);
} catch (Throwable $e) {
    responder(false, "Ocurrió un error en el servidor, inténtalo de nuevo");
}

$mensajes = [
    0 => "Datos actualizados",
    1 => "Usuario no encontrado",
    2 => "Acción no válida",
    3 => "Ese usuario ya está en uso",
    4 => "Ese correo ya está registrado",
    5 => "Todos los campos son obligatorios",
    6 => "Ese dato ya está en uso"
];

$codigo = (int) ($resultado["codigo"] ?? -1);
responder($codigo === 0, $mensajes[$codigo] ?? "Error desconocido");