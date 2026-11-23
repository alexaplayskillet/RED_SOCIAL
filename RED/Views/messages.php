<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<HTML>
    <HEAD>
         <meta charset="UTF-8">
        <title>Mensajes</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        
        <link href="./LIBS/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/distribucion.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/movil.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/menus.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/posts.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/fondo.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/botella.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/crearpub.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/messages.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/login.css">
        <link rel="stylesheet" TYPE="TEXT/CSS" href="./CSS/modo-claro.css">
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
        <!--CONTENEDOR DE LOS 3 PANELES-->
        <DIV CLASS="bodyy ver-chats" id="bodyMensajes">
            <DIV CLASS="menulat">
            <ul>
                 <!-- En PC lleva al inicio; en movil alterna entre la lista de chats y la conversacion -->
                 <a href="./index.php" class="cosmix-inicio" id="btnPlaneta" title="Inicio">
                        <ion-icon name="planet"></ion-icon>
                    </a>
                <li class="solo-movil" onclick="window.location.href='index.php'" title="Inicio"><ion-icon name="home-outline"></ion-icon></li>
                <li><ion-icon name="chatbubble-ellipses-outline"></ion-icon></li>
                <li>🍾</li>
            </ul>
            </DIV>

            <DIV CLASS="chats">
                <h3>Chats</h3>
                <ul id="listaChats"></ul>
                <p class="chats-vacio" id="chatsVacio">Aún no tienes conversaciones. Entra al perfil de un amigo y pulsa «Enviar mensaje».</p>
            </DIV>

            <DIV CLASS="conv sin-chat" id="conv">
                <div class="conv-vacio" id="convVacio">
                    <ion-icon name="chatbubbles-outline"></ion-icon>
                    <p>Selecciona un chat para empezar a conversar</p>
                </div>

                <DIV CLASS="userinfo" id="convCabecera">
                    <div class="conv-avatar" id="convAvatar"></div>
                    <DIV class="username">
                        <p id="convNombre"></p>
                        <p id="convTag"></p>
                    </DIV>
                </DIV>

                <DIV CLASS="mensajes" id="mensajes"></DIV>

                <form class="form-mensaje" id="formMensaje" autocomplete="off">

                    <button type="button" class="boton-emoji">
                        <ion-icon name="happy-outline"></ion-icon>
                    </button>

                    <input
                        class="inp"
                        id="inputMensaje"
                        name="texto"
                        type="text"
                        maxlength="255"
                        placeholder="Escribe un mensaje..."
                    >

                    <button type="submit" class="boton-enviar" id="btnEnviar">
                        <ion-icon name="send-outline"></ion-icon>
                    </button>

                </form>
            </DIV>
        </DIV>
        
        <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
        <script src="./LIBS/jquery-4.0.0.min.js"></script>
       
        <script src="./LIBS/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <script src="./JS/tema.js"></script>
        <SCRIPT SRC="./JS/particulas.js"></SCRIPT>
        <SCRIPT SRC="./JS/mensajes.js"></SCRIPT>
        
    </BODY>
    <FOOTER>

    </FOOTER>
</HTML>
