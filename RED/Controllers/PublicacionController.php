<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/PublicacionModelo.php";
require_once __DIR__ . "/../model/ComentarioModelo.php";
require_once __DIR__ . "/TiempoHelper.php";

function responder($datos, $codigoHttp = 200) {
    http_response_code($codigoHttp);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

// Si el procedimiento regreso un codigo de error, responde con el mensaje y termina
function revisarCodigo($fila) {
    $mensajes = [
        1 => "La publicación no existe.",
        2 => "Acción no válida.",
        3 => "Escribe algo o agrega una foto o video.",
        4 => "El texto no puede pasar de 255 caracteres.",
        5 => "El comentario no existe.",
        6 => "Solo puedes modificar tus propias publicaciones.",
        7 => "Error en la base de datos."
    ];

    $codigo = (int) ($fila["codigo"] ?? 7);

    if ($codigo !== 0) {
        responder(["ok" => false, "mensaje" => $mensajes[$codigo] ?? "Error desconocido."], 400);
    }
}

// Deja un comentario de la base de datos con el formato que usa el JavaScript
function formatearComentario($fila) {
    return [
        "id"         => (int) $fila["id_come"],
        "id_padre"   => $fila["id_padre"] !== null ? (int) $fila["id_padre"] : null,
        "id_usuario" => (int) $fila["id_usuario"],
        "nombre"     => $fila["nombre"],
        "texto"      => $fila["texto"],
        "fecha"      => $fila["fechaC"],
        "hace"       => tiempoRelativo($fila["segundos"], $fila["fechaC"]),
        "likes"      => (int) $fila["likes"],
        "dio_like"   => (int) $fila["dio_like"] === 1,
        "respuestas" => []
    ];
}

// Revisa el archivo subido en $_FILES[$campo]. Regresa [contenido, tipo] o [null, null] si no se subio nada.
// Solo acepta fotos y videos de maximo 10 MB.
function leerArchivo($campo) {
    $maxBytes = 10 * 1024 * 1024;
    $tiposOk  = [
        "image/jpeg", "image/png", "image/gif", "image/webp",
        "video/mp4", "video/webm", "video/ogg"
    ];

    if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }

    $archivo = $_FILES[$campo];

    if ($archivo["error"] === UPLOAD_ERR_INI_SIZE || $archivo["error"] === UPLOAD_ERR_FORM_SIZE) {
        responder(["ok" => false, "mensaje" => "El archivo pesa más de lo que permite el servidor (revisa upload_max_filesize y post_max_size en php.ini)."], 400);
    }

    if ($archivo["error"] !== UPLOAD_ERR_OK) {
        responder(["ok" => false, "mensaje" => "No se pudo subir el archivo."], 400);
    }

    if ($archivo["size"] > $maxBytes) {
        responder(["ok" => false, "mensaje" => "El archivo pesa más de 10 MB."], 400);
    }

    // El tipo se revisa con el contenido real del archivo, no con su nombre
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($archivo["tmp_name"]);
    if (!in_array($tipo, $tiposOk, true)) {
        responder(["ok" => false, "mensaje" => "Formato no permitido. Fotos: JPG, PNG, GIF o WEBP. Videos: MP4, WEBM u OGG."], 400);
    }

    return [file_get_contents($archivo["tmp_name"]), $tipo];
}

// Si el formulario es mas grande que post_max_size, PHP llega con $_POST vacio
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($_POST) && (int) ($_SERVER["CONTENT_LENGTH"] ?? 0) > 0) {
    responder(["ok" => false, "mensaje" => "El archivo es demasiado grande para el servidor (revisa post_max_size en php.ini)."], 400);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(["ok" => false, "mensaje" => "Método no permitido."], 405);
}

$accion = $_POST["accion"] ?? "";

// Sin cuenta solo se pueden leer los comentarios; publicar, comentar y dar like necesitan sesion
if (!isset($_SESSION["id_usuario"]) && $accion !== "comentarios") {
    responder(["ok" => false, "login" => true, "mensaje" => "Inicia sesión para hacer esto."], 401);
}

$idUsuario   = (int) ($_SESSION["id_usuario"] ?? 0);
$modelo      = new PublicacionModelo($conexion);
$modeloCom   = new ComentarioModelo($conexion);

if ($accion === "crear") {
    $texto = trim($_POST["texto"] ?? "");
    [$multimedia, $tipo] = leerArchivo("imagen");

    if ($texto === "" && $multimedia === null) {
        responder(["ok" => false, "mensaje" => "Escribe algo o agrega una foto o video."], 400);
    }

    if (mb_strlen($texto) > 255) {
        responder(["ok" => false, "mensaje" => "El texto no puede pasar de 255 caracteres."], 400);
    }

    $fila = $modelo->CrearPublicacion($idUsuario, $texto === "" ? null : $texto, $multimedia, $tipo);
    revisarCodigo($fila);

    responder(["ok" => true, "id_pub" => (int) $fila["id_pub"]]);

} elseif ($accion === "editar") {
    $idPub  = (int) ($_POST["id_pub"] ?? 0);
    $texto  = trim($_POST["texto"] ?? "");
    $quitar = ($_POST["quitar_media"] ?? "0") === "1";
    [$multimedia, $tipo] = leerArchivo("archivo");

    if (mb_strlen($texto) > 255) {
        responder(["ok" => false, "mensaje" => "El texto no puede pasar de 255 caracteres."], 400);
    }

    // Sin archivo nuevo: "QUITAR" borra el actual y null lo deja igual
    if ($multimedia === null) {
        $tipo = $quitar ? "QUITAR" : null;
    }

    $fila = $modelo->EditarPublicacion($idUsuario, $idPub, $texto === "" ? null : $texto, $multimedia, $tipo);
    revisarCodigo($fila);

    responder(["ok" => true]);

} elseif ($accion === "eliminar") {
    $idPub = (int) ($_POST["id_pub"] ?? 0);

    $fila = $modelo->EliminarPublicacion($idUsuario, $idPub);
    revisarCodigo($fila);

    responder(["ok" => true]);

} elseif ($accion === "like") {
    $idPub = (int) ($_POST["id_pub"] ?? 0);

    $fila = $modelo->AlternarLike($idUsuario, $idPub);
    revisarCodigo($fila);

    responder([
        "ok"       => true,
        "dio_like" => (int) $fila["dio_like"] === 1,
        "total"    => (int) $fila["total"]
    ]);

} elseif ($accion === "comentar") {
    $idPub   = (int) ($_POST["id_pub"] ?? 0);
    $texto   = trim($_POST["texto"] ?? "");
    $idPadre = isset($_POST["id_padre"]) && $_POST["id_padre"] !== "" ? (int) $_POST["id_padre"] : null;

    if ($texto === "") {
        responder(["ok" => false, "mensaje" => "El comentario está vacío."], 400);
    }

    if (mb_strlen($texto) > 255) {
        responder(["ok" => false, "mensaje" => "El comentario no puede pasar de 255 caracteres."], 400);
    }

    $fila = $modeloCom->Comentar($idUsuario, $idPub, $texto, $idPadre);
    revisarCodigo($fila);

    responder([
        "ok"         => true,
        "total"      => (int) $fila["total"],
        "comentario" => formatearComentario($fila)
    ]);

} elseif ($accion === "like_com") {
    $idCom = (int) ($_POST["id_com"] ?? 0);

    $fila = $modeloCom->AlternarLikeComentario($idUsuario, $idCom);
    revisarCodigo($fila);

    responder([
        "ok"       => true,
        "dio_like" => (int) $fila["dio_like"] === 1,
        "total"    => (int) $fila["total"]
    ]);

} elseif ($accion === "comentarios") {
    $idPub = (int) ($_POST["id_pub"] ?? 0);
    $filas = $modeloCom->CargarComentarios($idPub, $idUsuario);

    // Primero se formatean todos y luego cada respuesta se mete dentro de su comentario principal
    $porId = [];
    foreach ($filas as $fila) {
        $porId[(int) $fila["id_come"]] = formatearComentario($fila);
    }

    foreach ($porId as $id => $comentario) {
        $padre = $comentario["id_padre"];
        if ($padre !== null && isset($porId[$padre])) {
            $porId[$padre]["respuestas"][] = $comentario;
        }
    }

    $principales = [];
    foreach ($porId as $comentario) {
        if ($comentario["id_padre"] === null || !isset($porId[$comentario["id_padre"]])) {
            $principales[] = $comentario;
        }
    }

    responder(["ok" => true, "comentarios" => $principales]);

} else {
    responder(["ok" => false, "mensaje" => "Acción no válida."], 400);
}