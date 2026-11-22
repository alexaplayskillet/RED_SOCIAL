<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Las paginas que se pueden ver sin cuenta (index y verPublicacion) ponen $paginaPublica = true
// antes de incluir este archivo; las demas siguen mandando al login si no hay sesion
$paginaPublica = $paginaPublica ?? false;
$hayUsuario = isset($_SESSION["id_usuario"]);

if (!$hayUsuario && !$paginaPublica) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/UsuarioModelo.php";

if (!function_exists("h")) {
    function h($texto) {
        return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
    }
}

$v = time();

if ($hayUsuario) {
    $modeloHeader = new UsuarioModelo($conexion);
    $perfilHeader = $modeloHeader->obtenerPerfil((int) $_SESSION["id_usuario"]);

    if (!$perfilHeader) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    $tagHeader = $perfilHeader["tag"];
    $nombreHeader = trim($perfilHeader["nombre"]) !== "" ? $perfilHeader["nombre"] : $tagHeader;
} else {
    $tagHeader = "";
    $nombreHeader = "Invitado";
}

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

// Foto del usuario para mostrar en la pagina. Si no tiene foto se ven sus iniciales;
// a los visitantes sin sesion se les muestra una silueta generica, nunca una foto.
if ($hayUsuario) {
    $svgFoto = '<svg xmlns="http://www.w3.org/2000/svg" width="90" height="90"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8e5cdc"/><stop offset="1" stop-color="#3cc8e6"/></linearGradient></defs><rect width="90" height="90" fill="url(#g)"/><text x="45" y="58" font-family="Arial,sans-serif" font-size="34" font-weight="700" fill="#ffffff" text-anchor="middle">' . h($inicialesHeader) . '</text></svg>';
} else {
    $svgFoto = '<svg xmlns="http://www.w3.org/2000/svg" width="90" height="90"><rect width="90" height="90" fill="#3a2f55"/><circle cx="45" cy="35" r="16" fill="#8f86a8"/><path d="M14 84c3-19 16-28 31-28s28 9 31 28z" fill="#8f86a8"/></svg>';
}

$fotoPorDefecto = "data:image/svg+xml;utf8," . rawurlencode($svgFoto);
$fotoUsuarioSrc = $hayUsuario ? "../Controllers/ImagenController.php?tipo=foto&v=" . $v : $fotoPorDefecto;