<?php
session_start();

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/ReporteModelo.php";
require_once __DIR__ . "/../model/EstadisticasModelo.php";

function responderJson($datos, $codigo = 200) {
    http_response_code($codigo);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function fechaLegible($fecha) {
    return date("d/m/Y H:i", strtotime($fecha));
}

function nombreMostrado($fila) {
    return trim($fila["nombre"]) !== "" ? $fila["nombre"] : $fila["tag"];
}

// Evita que Excel ejecute como formula un texto escrito por un usuario
function celdaSegura($valor) {
    if (is_string($valor) && $valor !== "" && strpos("=+-@\t\r", $valor[0]) !== false) {
        return "'" . $valor;
    }
    return $valor;
}

if (!isset($_SESSION["id_usuario"])) {
    responderJson(["ok" => false, "mensaje" => "Sesión no iniciada."], 401);
}

$idUsuario = (int) $_SESSION["id_usuario"];
$tipo = $_GET["tipo"] ?? "";
$formato = $_GET["formato"] ?? "json";

if (!in_array($tipo, ["general", "amigos", "publicaciones"], true) || !in_array($formato, ["json", "csv"], true)) {
    responderJson(["ok" => false, "mensaje" => "Solicitud no válida."], 400);
}

$modelo = new ReporteModelo($conexion);
$meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio",
          "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
$periodo = $meses[(int) date("n") - 1] . " de " . date("Y");

if ($tipo === "amigos") {
    $titulo = "Amigos agregados en " . $periodo;
    $columnas = ["Fecha", "Persona", "Usuario", "Detalle"];
    $filas = [];

    foreach ($modelo->amigosDelMes($idUsuario) as $f) {
        $filas[] = [
            fechaLegible($f["fecha"]),
            nombreMostrado($f),
            "@" . $f["tag"],
            (int) $f["envie"] === 1
                ? "Aceptó tu solicitud de amistad"
                : "Aceptaste su solicitud de amistad"
        ];
    }

} elseif ($tipo === "publicaciones") {
    $titulo = "Publicaciones creadas en " . $periodo;
    $columnas = ["Fecha", "Tipo", "Texto", "Me gusta", "Comentarios", "Guardados"];
    $filas = [];

    foreach ($modelo->publicacionesDelMes($idUsuario) as $f) {
        $filas[] = [
            fechaLegible($f["fecha"]),
            $f["tipo"],
            (string) ($f["texto"] ?? ""),
            (int) $f["likes"],
            (int) $f["comentarios"],
            (int) $f["guardados"]
        ];
    }

} else {
    $titulo = "Resumen general";
    $columnas = ["Indicador", "Valor"];
    $est = new EstadisticasModelo($conexion);

    $filas = [
        ["Me gusta recibidos", $est->obtenerLikes($idUsuario)],
        ["Comentarios recibidos", $est->obtenerComentarios($idUsuario)],
        ["Amigos totales", $est->obtenerAmigos($idUsuario)],
        ["Amigos agregados en " . $periodo, count($modelo->amigosDelMes($idUsuario))],
        ["Publicaciones en " . $periodo, $est->obtenerPublis($idUsuario)]
    ];
}

if ($formato === "json") {
    responderJson(["ok" => true, "titulo" => $titulo, "columnas" => $columnas, "filas" => $filas]);
}

// CSV: con BOM para que Excel respete los acentos
$archivo = "cosmix_" . $tipo . "_" . date("Y-m") . ".csv";
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"" . $archivo . "\"");

$salida = fopen("php://output", "w");
fwrite($salida, "\xEF\xBB\xBF");
fputcsv($salida, $columnas, ",", "\"", "\\");
foreach ($filas as $fila) {
    fputcsv($salida, array_map("celdaSegura", $fila), ",", "\"", "\\");
}
fclose($salida);
