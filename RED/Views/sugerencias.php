<?php require_once __DIR__ . "/usuario_header.php"; ?>
<!DOCTYPE HTML>
<HTML>
    <HEAD>
        <meta charset="UTF-8">
        <title>Sugerencias de amistad</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


        <link href="./LIBS/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/distribucion.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/movil.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/menus.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/notificaciones.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/posts.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/fondo.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/botella.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/crearpub.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/solicitudes.css">
        <link rel="stylesheet" href="./CSS/modo-claro.css">

             <script>
                const temaInicial = localStorage.getItem("cosmix-tema") || "dark";
                document.documentElement.classList.add(
                    temaInicial === "light" ? "modo-claro" : "modo-oscuro"
                );
            </script>
    </HEAD>
    <BODY>
            <!--Elementos para el fondo-->
        <div class="fondo_cos">
            <div class="gradi1"></div>
            <div class="gradi2"></div>
            <div class="gradi3"></div>
            <div class="circulo1"></div>
            <div class="circulo2"></div>
            <div class="circulo3"></div>
            <canvas id="particulas"></canvas>
        </div>
        <div class="figuras"></div>

        <!--Acomodamos los menus antes que el contenido que siempre se vera-->
        <DIV CLASS="menu">
            <div class="menu-logo">
                <span><i class="ion-md-planet"></i> COSMIX</span>
        <button class="hamburger hamburger-close" accesskey="M">☰</button>
    </div>
    <ul class="menulist">
        
        <li class="active" onclick="window.location.href='index.php'"><i class="ion-md-home"></i> Inicio</li>
         <li onclick="window.location.href='messages.php'"><i class="ion-md-mail"></i> Chat</li>
        <li><i class="ion-md-bookmark"></i> Guardados</li>
        <li onclick="window.location.href='Solicitudes.php'"><i class="ion-md-people"></i> Solicitudes de amistad</li>
    </ul>
    <button class="create-post-btn">Publicar</button>
    </DIV>

        <?php require __DIR__ . "/menu_usuario.php"; ?>

        <?php require __DIR__ . "/notificaciones_panel.php"; ?>

        <DIV CLASS="title">
            <button id="toggleMenu" class="hamburger" accesskey="M">☰</button>
            <div class="title-logo"><i class="ion-md-planet"></i> COSMIX</div>
            <div class="search-box"><i class="ion-md-search"></i> Buscar</div>
            <div class="title-icons">
                <i class="ion-md-notifications-outline" id="notifTrigger"></i>
                <i class="ion-md-mail-outline"></i>
                <div class="profile" id="profileTrigger" style="overflow:hidden;" onclick="document.getElementById('profileMenu').classList.toggle('active')">
                    <?= avatarUsuario("headerAvatar","headerIniciales",$inicialesHeader,$v) ?>
                </div>
            </div>
        </DIV>
        <DIV CLASS="body">

            
    <DIV CLASS="body solicitudes-page">

    <div class="solicitudes-header">
        <h2>Sugerencias de amistad</h2>
        <p class="solicitudes-subtitulo">Personas que quizás conozcas</p>
        <a class="ver-mas-sugerencias" href="Solicitudes.php" style="text-align:left">Ver mis solicitudes recibidas</a>
    </div>

            <p class="solicitudes-msg" id="solicitudesMensaje"></p>

            <DIV CLASS="CajaSoli w100" id="sugerenciasGrid"></DIV>
            <p class="solicitudes-vacio" id="solicitudesVacio"></p>

            <div id="mensaje-card">
                <h4>Mensaje a la deriva</h4>
                <p id="mensaje-texto">Cargando...</p>
                <div class="acciones">
                    <button id="btn-devolver">Devolver al mar</button>
                    <button id="btn-cerrar">Recoger la botella</button>
                </div>
            </div>

        </DIV>
        <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
    </BODY>
    <FOOTER>
        <script src="./LIBS/jquery-4.0.0.min.js"></script>
       
        <script src="./LIBS/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <SCRIPT SRC="./JS/buttons.js"></SCRIPT>
        <script src="./JS/notificaciones.js"></script>
        <SCRIPT SRC="./JS/particulas.js"></SCRIPT>
        <SCRIPT SRC="./JS/deriva.js"></SCRIPT>
        <script src="./JS/crearpub.js"></script>
        <script src="./JS/tema.js"></script>
        <script src="./JS/solicitudes.js"></script>
        
    </FOOTER>
</HTML>