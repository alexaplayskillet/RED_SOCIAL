<?php
    require_once '../model/conexion.php';
    require_once '../model/UsuarioModelo.php';

    /*creo el objeto de modelo con la conexion a la bd*/
    session_start();
    $modelo = new UsuarioModelo($conexion);

    /*checar si se hizo el envio del registro*/

    //revisar si el formulario se envio
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //recupero los datos y quito espacios
        $usuario = trim($_POST['user'] ?? '');
        $password = $_POST['password'] ?? '';

        //validar que no esten vacios los inputs
        if ($usuario === '' || $password === ''){
            $_SESSION['error_login'] = "Todos los campos son obligatorios";
            header("Location: ../Views/login.php");
            exit();
        };

        //llamar al modelo
        $credenciales = $modelo->obtenerUsuarioTag($usuario);

        //validar la informacion
        if ($credenciales && password_verify($password, $credenciales['hash'])){
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $credenciales['id'];
            header("Location:../Views/index.php");
        }else{
            $_SESSION['error_login'] = "El tag de usuario o la contraseña es incorrecta";
            header("Location: ../Views/login.php");
        }

        exit;
    }else{
        header("Location: ../Views/login.php");
        exit;
    }


?>