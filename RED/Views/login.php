<?php
    session_start();
    $error = $_SESSION['error_login'] ?? '';
    unset($_SESSION['error_login']);
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Iniciar Sesion</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--Aqui iran las ligas de elementos externos como iconos y tipografias-->
        <!--aqui van las ligas externas-->
            <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

            
        <!--Aqui iran las ligas de los elementos de css-->
        <link href="./LIBS/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/distribucion.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/movil.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/menus.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/posts.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/login.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/validaciones.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/fondo.css">
      <link rel="stylesheet" href="./CSS/modo-claro.css">

             <script>
                const temaInicial = localStorage.getItem("cosmix-tema") || "dark";
                document.documentElement.classList.add(
                    temaInicial === "light" ? "modo-claro" : "modo-oscuro"
                );
            </script>
        </head>
    <body>
       <?php if ($error !== ''): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    <div class="fondo_cos fondo-login">
        <div class="gradi1"></div>
        <div class="gradi2"></div>
        <div class="gradi3"></div>
        <div class="circulo1"></div>
        <div class="circulo2"></div>
        <div class="circulo3"></div>
        <div class="circulo4"></div>
        <canvas id="particulas"></canvas>
    </div>
<div class="figuras"></div>
        <DIV class="log allscreen">
            <div class="formulario">
                <div class="cabecera">
                    <div class="login-logo"><i class="ion-md-planet"></i> COSMIX</div>
                    <H1>Iniciar Sesión</H1>
                </div>
                
                <form action="../Controllers/LoginController.php" method="POST">
                    <div class="input-group">
                        <i class="ion-md-person input-icon"></i>
                        <input type="text" id="user" name="user" placeholder="Tag del usuario" class="inp">
                    </div>
                    <div CLASS="validations">
                        <small class="pls">Por favor ingresa el tag de usuario</small>
                        <small class="error">El tag del usuario debe tener al menos 4 caracteres, puede incluir numeros y guiones bajos</small>
                    </div>

                   <div class="input-group">
                        <i class="ion-md-lock input-icon"></i>
                        <input type="password" id="password" class="inp" placeholder="Contraseña" required tabindex="0">
                    </div>
                    <div CLASS="validations">
                        <small class="pls">Por favor ingresa password</small>
                        <small class="error">La contraseña debe tener al menos 4 caracteres, puede incluir numeros y guiones bajos</small>
                    </div>


                    <DIV CLASS="enviar">
                        <INPUT TYPE="submit" CLASS="btn" tabindex="0" VALUE="INGRESAR">
                    </DIV>
                </form>
                <a href="register.php">¿No tienes una cuenta? ¡Registrate ahora!</a>
            </div>
            
        </DIV>
         <!--añadir ligas externas-->
        <script src="./LIBS/jquery-4.0.0.min.js"></script>
       
        <script src="./LIBS/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <SCRIPT SRC="./JS/Logvalidate.js"></SCRIPT>
        <SCRIPT SRC="./JS/particulas.js"></SCRIPT>
        <script src="./JS/tema.js"></script>
        
    </body>
    <footer>
    </footer>
</html>