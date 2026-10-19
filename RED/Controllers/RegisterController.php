<?php
    require_once '../model/conexion.php';
    require_once '../model/UsuarioModelo.php';

    /*creo el objeto de modelo con la conexion a la bd*/
    session_start();
    $modelo = new UsuarioModelo($conexion);

    //recibo los datos y valido su contenido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['nombre']) || empty($_POST['usuario']) || empty($_POST['fecha']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password2']) ){
            $_SESSION['error_registro'] = "Todos los campos son obligatorios";
            header("Location: ../Views/register.php");
            exit();
        }

        //recupero los datos

        $nombre = trim($_POST['Nombre']);
        $tag = trim($_POST['usuario']);
        $fechaNac = $_POST['fecha'];
        $correo = trim($_POST['email']);
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        //hago las validaciones
        //validar formato (las mismas reglas que el JS)
        if (mb_strlen($nombre) > 100 || !preg_match('/^[a-zA-Z0-9_-]{4,16}$/', $tag) || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($password) < 4 || strlen($password2) < 4){
            /*header("Location: ../register.php?error=formato");*/
            $_SESSION['error_registro'] = "error de formato";
            header("Location: ../Views/register.php");
            exit();
        }
        $f = DateTime::createFromFormat('Y-m-d', $fechaNac);
        if (!$f || $f->format('Y-m-d') !== $fechaNac || $f > new DateTime()) {
             $_SESSION['error_registro'] = "la fecha no es valida, apunta al futuro";
            header("Location: ../Views/register.php");
            exit();
        }

        if($password != $password2){
            /*header("Location: ../register.php?error=contraseñas no iguales");*/
            $_SESSION['error_registro'] = "contraseñas no coinciden";
            header("Location: ../Views/register.php");
            exit();
        }

        //encriptamos la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);


        //llamamos al procedure
        $insertar = $modelo->registrarUsuario($nombre, $tag, $fechaNac, $correo, $hash);

        switch($insertar['codigo']){
            case 0:
                session_regenerate_id(true);
                $_SESSION['id_usuario'] = $insertar['id'];
                header("Location: ../Views/index.php");
            break;
            case 1:
                $_SESSION['error_registro'] = "Ese tag ya está en uso";
                header("Location: ../Views/register.php");
            break;
            case 2:
                $_SESSION['error_registro'] = "Ese correo ya está registrado";
                header("Location: ../Views/register.php");
            break;
            default:
                $_SESSION['error_registro'] = "No se pudo completar el registro, inténtalo de nuevo";
                header("Location: ../Views/register.php");
            }
        exit;
        

    }else{
        header("Location: ../Views/register.php");
        exit();
    }
?>