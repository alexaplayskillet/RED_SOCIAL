<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/UsuarioModelo.php";

$modeloHeader = new UsuarioModelo($conexion);
$perfilHeader = $modeloHeader->obtenerPerfil((int) $_SESSION["id_usuario"]);

if (!$perfilHeader) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!function_exists("h")) {
    function h($texto) {
        return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
    }
}

$v = time();
$tagHeader = $perfilHeader["tag"];
$nombreHeader = trim($perfilHeader["nombre"]) !== "" ? $perfilHeader["nombre"] : $tagHeader;

$partesHeader = preg_split("/\s+/", trim($nombreHeader));
if (count($partesHeader) > 1) {
    $inicialesHeader = mb_strtoupper(mb_substr($partesHeader[0], 0, 1) . mb_substr($partesHeader[1], 0, 1));
} else {
    $inicialesHeader = mb_strtoupper(mb_substr($nombreHeader, 0, 2));
}

function avatarUsuario($idImg, $idIniciales, $iniciales, $v) {
    return '<img id="' . $idImg . '" alt="Foto de perfil" '
         . 'src="../Controllers/ImagenController.php?tipo=foto&v=' . $v . '" '
         . 'style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;" '
         . 'onload="this.style.display=\'block\'; document.getElementById(\'' . $idIniciales . '\').style.display=\'none\'" '
         . 'onerror="this.style.display=\'none\'">'
         . '<span id="' . $idIniciales . '">' . h($iniciales) . '</span>';
}