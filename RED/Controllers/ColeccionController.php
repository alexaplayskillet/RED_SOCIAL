<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../model/conexion.php';
require_once __DIR__ . '/../model/ColeccionModelo.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sesión no iniciada']);
    exit;
}

$usuario = (int) $_SESSION['id_usuario'];
$modelo  = new ColeccionModelo($conexion); 
$accion  = $_REQUEST['accion'] ?? '';

try {
    switch ($accion) {
        case 'listar':
            $datos = $modelo->listarColecciones($usuario);
            echo json_encode(['ok' => true, 'datos' => $datos]);
            break;

        case 'crear':
            $nombre = trim($_POST['nombre'] ?? '');
            $desc   = trim($_POST['descripcion'] ?? '');
            $id = $modelo->crearColeccion($usuario, $nombre, $desc);
            echo json_encode(['ok' => true, 'id' => $id]);
            break;

        case 'guardar':
            $modelo->guardarPublicacion($usuario, (int)$_POST['id_tablero'], (int)$_POST['id_pub']);
            echo json_encode(['ok' => true]);
            break;

        case 'quitar':
            $modelo->quitarGuardado($usuario, (int)$_POST['id_tablero'], (int)$_POST['id_pub']);
            echo json_encode(['ok' => true]);
            break;

        case 'guardados':
            $datos = $modelo->listarGuardados($usuario, (int)($_GET['id_tablero'] ?? 0));
            echo json_encode(['ok' => true, 'datos' => $datos]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Acción no válida']);
    }
} catch (mysqli_sql_exception $e) {
    /* 1644 = SIGNAL de nuestros procedimientos (mensaje seguro para mostrar) */
    $msg = ($e->getCode() === 1644) ? $e->getMessage() : 'Error en la base de datos';
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $msg]);
}