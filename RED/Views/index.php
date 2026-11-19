<?php
    session_start();//para recordar quién inició sesión
    //que incluya la conexion antes de hacer nada
    if (!isset($_SESSION['id_usuario'])) {
      header("Location: login.php");
      exit();
  }

  require_once '../Controllers/ShowPublicationsController.php';
?>

<!DOCTYPE HTML>
<HTML>
    <HEAD>
        <meta charset="UTF-8">
        <title>Pagina Principal</title>
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
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/funcionalidades.css">
        <link rel="stylesheet" TYPE="TEXT/CSS" href="./CSS/modo-claro.css">

        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/solicitudes.css">
        
             <script>
                const temaInicial = localStorage.getItem("cosmix-tema") || "dark";
                document.documentElement.classList.add(
                    temaInicial === "light" ? "modo-claro" : "modo-oscuro"
                );
            </script>
    </HEAD>

    <BODY>

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

        <DIV CLASS="menu">

            <div class="menu-logo">
                <span><i class="ion-md-planet"></i> COSMIX</span>
                <button class="hamburger hamburger-close" accesskey="M">☰</button>
            </div>

            <ul class="menulist">

                <li class="active" onclick="window.location.href='index.php'">
                    <i class="ion-md-home"></i> Inicio
                </li>

                <li onclick="window.location.href='messages.html'">
                    <i class="ion-md-mail"></i> Chat
                </li>

                <li onclick="window.location.href='colecciones.html'">
                    <i class="ion-md-bookmark"></i> Guardado
                </li>

                <li onclick="window.location.href='Solicitudes.php'">
                    <i class="ion-md-people"></i> Solicitudes de amistad
                </li>

            </ul>

            <button class="create-post-btn">Publicar</button>

        </DIV>

        <?php require __DIR__ . "/menu_usuario.php"; ?>

        <DIV CLASS="notif-menu" id="notifMenu">

            <div class="notif-header">
                <h3>Notificaciones</h3>
            </div>

            <div class="notif-tabs">

                <div class="notif-tab active" data-notiftab="todas">
                    Todas
                </div>

                <div class="notif-tab" data-notiftab="noleidas">
                    No leídas
                </div>

            </div>

            <div class="notif-aviso" id="notifAviso">

                <div class="notif-aviso-top">

                    <div>

                        <p class="notif-aviso-titulo">
                            Las notificaciones de mensaje a la deriva estan activadas
                        </p>

                        <p class="notif-aviso-desc">
                            Desactiva en caso que lo necesites
                        </p>

                    </div>

                    <button class="notif-aviso-cerrar" id="notifAvisoCerrar">
                        <i class="ion-md-close"></i>
                    </button>

                </div>

                <div class="notif-aviso-btns">

                    <button class="btn-notif-activar">
                        Desactivar
                    </button>

                    <button class="btn-notif-ahora-no" id="notifAhoraNo">
                        Ahora no
                    </button>

                </div>

            </div>

            <div class="notif-lista">

                <div class="notif-grupo-titulo">

                    <span>Nuevas</span>

                    <a class="notif-ver-todo">
                        Ver todo
                    </a>

                </div>

                <div class="notif-item">

                    <div class="notif-avatar">

                        <img src="./resources/foto.jpg" alt="usuario">

                        <span class="notif-badge notif-badge-insignia">
                            <i class="ion-md-ribbon"></i>
                        </span>

                    </div>

                    <div class="notif-texto">

                        <p>
                            <strong>¡Felicitaciones!</strong>
                            Obtuviste una insignia de fan destacado.
                        </p>

                        <span class="notif-tiempo">
                            2 días
                        </span>

                    </div>

                    <span class="notif-dot"></span>

                </div>

                <div class="notif-item">

                    <div class="notif-avatar">

                        <img src="./resources/noticia1.png" alt="pagina">

                        <span class="notif-badge notif-badge-campana">
                            <i class="ion-md-notifications"></i>
                        </span>

                    </div>

                    <div class="notif-texto">

                        <p>
                            <strong>Taste Buds with Yezi</strong>
                            te invitó a que sigas su página.
                        </p>

                        <span class="notif-tiempo">
                            2 días
                        </span>

                    </div>

                    <span class="notif-dot"></span>

                </div>

                <div class="notif-grupo-titulo notif-grupo-anteriores">
                    <span>Anteriores</span>
                </div>

                <div class="notif-item">

                    <div class="notif-avatar">

                        <img src="./resources/foto.jpg" alt="usuario">

                        <span class="notif-badge notif-badge-insignia">
                            <i class="ion-md-ribbon"></i>
                        </span>

                    </div>

                    <div class="notif-texto">

                        <p>
                            <strong>¡Felicitaciones!</strong>
                            Obtuviste una insignia de fan destacado.
                        </p>

                        <span class="notif-tiempo">
                            3 días
                        </span>

                    </div>

                    <span class="notif-dot"></span>

                </div>

                <div class="notif-item">

                    <div class="notif-avatar">

                        <img src="./resources/foto.jpg" alt="usuario">

                        <span class="notif-badge notif-badge-personas">
                            <i class="ion-md-people"></i>
                        </span>

                    </div>

                    <div class="notif-texto">

                        <p>
                            Te damos la bienvenida a
                            <strong>Personas que sé...</strong>
                            Ahora puedes publicar contenido, conectarte...
                        </p>

                        <span class="notif-tiempo">
                            5 días
                        </span>

                    </div>

                    <span class="notif-dot"></span>

                </div>

            </div>

        </DIV>

        <DIV CLASS="title">

            <button id="toggleMenu" class="hamburger" accesskey="M">
                ☰
            </button>

            <div class="title-logo">
                <i class="ion-md-planet"></i> COSMIX
            </div>

            <div class="search-box">
                <i class="ion-md-search"></i> Buscar
            </div>

            <div class="title-icons">

                <i class="ion-md-notifications-outline"
                   id="notifTrigger"
                   onclick="document.getElementById('notifMenu').classList.toggle('active')">
                </i>

                <i class="ion-md-mail-outline"></i>

                <div class="profile"
                     id="profileTrigger" style="overflow:hidden;"
                     onclick="document.getElementById('profileMenu').classList.toggle('active')">
                    <?= avatarUsuario("headerAvatar","headerIniciales",$inicialesHeader,$v) ?>
                </div>

            </div>

        </DIV>

        <?php require __DIR__ . "/crear_publicacion.php"; ?>

        <DIV CLASS="body">

            <DIV CLASS="ctr">

                <DIV CLASS="panel crear-post" id="crear">

                    <img src="./resources/foto.jpg"
                         alt="foto usuario"
                         class="foto">

                    <input type="text"
                           class="inputs crear-post-input"
                           placeholder="¿Qué está pasando por tu mente?">

                    <div class="crear-post-icons">

                        <button class="icon-btn"
                                title="Agregar foto">

                            <i class="ion-md-image"></i>

                        </button>

                        <button class="icon-btn"
                                title="Agregar video">

                            <i class="ion-md-videocam"></i>

                        </button>

                    </div>

                </DIV>

                <!--<DIV CLASS="panel">

                    <div class="panel-title">
                        Historias
                    </div>

                    <div class="stories-carousel">

                        <button class="stories-flecha stories-flecha-izq"
                                title="Anterior">

                            <i class="ion-md-arrow-back"></i>

                        </button>

                        <div class="crear-historia-fija"
                             onclick="abrirHistoria()">

                            <div class="story-crear-contenido">

                                <div class="story-crear-icono">
                                    <i class="ion-md-add"></i>
                                </div>

                                <span>
                                    Crear historia
                                </span>

                            </div>

                        </div>

                        <div class="stories" id="storiesContainer">

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario1</span>

                            </div>

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario2</span>

                            </div>

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario3</span>

                            </div>

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario3</span>

                            </div>

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario3</span>

                            </div>

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario3</span>

                            </div>

                            <div class="story">

                                <div class="story-ring">
                                    <div></div>
                                </div>

                                <span>@usuario3</span>

                            </div>

                        </div>

                        <button class="stories-flecha stories-flecha-der"
                                title="Siguiente">

                            <i class="ion-md-arrow-forward"></i>

                        </button>

                    </div>

                </DIV>
-->
                <!-- Apartado filtrar publis funcionalidad 2 -->

                <DIV CLASS="panel filtros-panel">

                    <div class="filtros-wrapper">

                        <button class="filtros-btn" id="filtrosBtn">
                            <i class="ion-md-options"></i>
                            Filtros
                        </button>

                        <div class="filtros-menu" id="filtrosMenu">

                            <div class="filtro-opcion active" data-filtro="amigos">

                                <i class="ion-md-people"></i>
                                <span>Amigos</span>
                                <i class="ion-md-checkmark filtro-check"></i>

                            </div>

                            <div class="filtro-opcion" data-filtro="recientes">

                                <i class="ion-md-time"></i>
                                <span>Más recientes</span>
                                <i class="ion-md-checkmark filtro-check"></i>

                            </div>

                            <div class="filtro-opcion" data-filtro="likes">

                                <i class="ion-md-heart"></i>
                                <span>Mayor cantidad de likes</span>
                                <i class="ion-md-checkmark filtro-check"></i>

                            </div>

                        </div>

                    </div>

                </DIV>
                
                <?php foreach ($publicaciones as $publicacion): ?>
                   
                    <article CLASS="post" data-id-pub="<?= (int)$publicacion['id_pub'] ?>">

                        <DIV CLASS="userinfo">

                            <img src="<?= $publicacion['foto_src']?>"
                                alt="no cargo"
                                class="foto">

                            <DIV class="username">

                                <p>
                                    <?= htmlspecialchars($publicacion['nombre'])?>

                                </p>

                                <p>
                                    <a href="verPublicacion.php?id=<?= (int)$publicacion['id_pub'] ?>"
                                    class="post-enlace">
                                        <?= htmlspecialchars($publicacion['fecha_pub']) ?>
                                    </a>
                                </p>

                            </DIV>

                        </DIV>

                        <div class="post-text">

                            <?= htmlspecialchars($publicacion['texto'])?>
                            <img src="<?= $publicacion['media_src']?>" alt="no hay archivo multimedia">
                        </div>
                    
                        <DIV CLASS="post-actions">

                            <span>
                                <i class="ion-md-heart-empty"></i>
                                <?= htmlspecialchars($publicacion['likes'])?>
                            </span>

                            <span onclick="this.closest('.post').querySelector('.caja_com').classList.toggle('active')">

                                <i class="ion-md-chatbubbles"></i>
                            <?= htmlspecialchars($publicacion['comentarios'])?>

                            </span>

                            <span>

                                <i class="ion-md-share"></i>
                            <?= htmlspecialchars($publicacion['compartidos'])?>

                            </span>

                            <span>

                                <i class="ion-md-bookmark"></i>
                                <?= htmlspecialchars($publicacion['guardados'])?>

                            </span>

                        </DIV>

                        <DIV CLASS="caja_com">

                            <FORM>

                                <INPUT TYPE="text"
                                    PLACEHOLDER="Escribe tu comentario"
                                    CLASS="inputs">

                            </FORM>

                        </DIV>
                        
                </article>
                    <?php endforeach; ?>
                
           
            
            </DIV>
            <DIV CLASS="right">

                <DIV CLASS="panel">

                    <div class="panel-title">
                        Sugerencias para ti
                    </div>

                    <div id="sugerenciasLista"></div>

                    <p class="solicitudes-msg" id="solicitudesMensaje"></p>

                    <a class="ver-mas-sugerencias" href="sugerencias.php">Ver más sugerencias</a>

                </DIV>

                <DIV CLASS="panel">

                    <div class="panel-title">
                        Noticias
                    </div>

                    <div class="noticia-item">

                        <img src="./resources/noticia1.png"
                             alt="noticia"
                             class="noticia-thumb">

                        <div class="noticia-info">

                            <p class="noticia-titulo">
                                New Jax and Gloink Queen Plushes!
                            </p>

                            <p class="noticia-descrip">
                                Coleccion limitada de peluches inspirados
                                en el universo Digital Circus.
                            </p>

                            <p class="noticia-fuente">
                                glitchproductions.store
                            </p>

                        </div>

                    </div>

                    <div class="noticia-item">

                        <img src="./resources/noticia2.png"
                             alt="noticia"
                             class="noticia-thumb">

                        <div class="noticia-info">

                            <p class="noticia-titulo">
                                Prueba gratis de Acrobat Pro por 7 dias
                            </p>

                            <p class="noticia-descrip">
                                Edita, firma y envia documentos PDF
                                desde cualquier dispositivo.
                            </p>

                            <p class="noticia-fuente">
                                adobe.com
                            </p>

                        </div>

                    </div>

                </DIV>

            </DIV>

            <div id="botella-flotante"
                 title="Mensaje a la deriva">

                🍾

            </div>

            <div id="mensaje-card">

                <h4>
                    Mensaje a la deriva
                </h4>

                <p id="mensaje-texto">
                    Cargando...
                </p>

                <div class="acciones">

                    <button id="btn-devolver"
                            class="btn-devolver">

                        Devolver al mar

                    </button>

                    <button id="btn-cerrar"
                            class="btn-cerrar">

                        Recoger la botella

                    </button>

                </div>

            </div>

        </DIV>

        <div id="contador-tiempo"
             title="Tiempo navegando en COSMIX">

            <i class="ion-md-time"></i>

            <span id="contador-valor">
                00:00:00
            </span>
                    
        </div>

        <DIV CLASS="historia-overlay"
             id="historiaOverlay">

            <DIV CLASS="historia-modal">

                <!-- Encabezado -->

                <div class="historia-modal-header">

                    <div>

                        <h2>
                            Crear una historia
                        </h2>

                        <span>
                            Comparte algo con tus amigos
                        </span>

                    </div>

                    <button class="historia-cerrar"
                            onclick="cerrarHistoria">

                        <i class="ion-md-close"></i>

                    </button>

                </div>

                <!-- Usuario -->

                <div class="historia-usuario">

                    <img src="./resources/foto.jpg"
                         alt="foto usuario">

                    <div>

                        <strong>
                            @<?= h($tagHeader) ?>
                        </strong>

                        <span>
                            Tu historia
                        </span>

                    </div>

                </div>

                <!-- Opciones para subir imagen o video -->

                <div class="historia-opciones"
                     id="historiaOpciones">

                    <label class="historia-opcion historia-imagen">

                        <input type="file"
                               id="historiaImagen"
                               accept="image/*"
                               onchange="seleccionarHistoria(this, 'imagen')">

                        <div class="historia-opcion-icono">

                            <i class="ion-md-image"></i>

                        </div>

                        <strong>
                            Imagen
                        </strong>

                        <span>
                            Comparte una foto
                        </span>

                    </label>

                    <label class="historia-opcion historia-video">

                        <input type="file"
                               id="historiaVideo"
                               accept="video/*"
                               onchange="seleccionarHistoria(this, 'video')">

                        <div class="historia-opcion-icono">

                            <i class="ion-md-videocam"></i>

                        </div>

                        <strong>
                            Video
                        </strong>

                        <span>
                            Comparte un video
                        </span>

                    </label>

                </div>

                <!-- Previsualización -->

                <div class="historia-preview"
                     id="historiaPreview">

                    <div class="historia-preview-contenido"
                         id="historiaPreviewContenido">
                    </div>

                </div>

                <!-- ===== FILTROS ===== -->

                <div class="historia-filtros"
                     id="historiaFiltros">

                    <div class="historia-filtros-linea"></div>

                    <h3>
                        Filtro
                    </h3>

                    <div class="historia-filtros-grid">

                        <button class="historia-filtro-btn active"
                                data-filtro="original"
                                type="button">

                            Original

                        </button>

                        <button class="historia-filtro-btn"
                                data-filtro="brillo"
                                type="button">

                            Brillo

                        </button>

                        <button class="historia-filtro-btn"
                                data-filtro="grises"
                                type="button">

                            Blanco y negro

                        </button>

                        <button class="historia-filtro-btn"
                                data-filtro="sepia"
                                type="button">

                            Sepia

                        </button>

                    </div>

                    <button class="historia-aplicar-filtro"
                            id="historiaAplicarFiltro"
                            type="button">

                        Aplicar filtro

                    </button>
                    <button class="historia-borrar-filtro"
                            id="historiaBorrarFiltro"
                            type="button">

                        Borrar

                    </button>

                    <div class="historia-filtros-linea"></div>

                </div>

                <!-- Botones finales -->

                <div class="historia-footer"
                     id="historiaFooter">

                    <button class="historia-cancelar"
                            onclick="cerrarHistoria()"
                            type="button">

                        Cancelar

                    </button>

                    <button class="historia-publicar"
                            id="historiaPublicar"
                            disabled
                            onclick="publicarHistoria()"
                            type="button">

                        Compartir historia

                    </button>

                </div>

            </DIV>

        </DIV>

        <script type="module"
                src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js">
        </script>

        <script nomodule
                src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js">
        </script>
        <script src="./LIBS/jquery-4.0.0.min.js"></script>
       
        <script src="./LIBS/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <SCRIPT SRC="./JS/buttons.js"></SCRIPT>
        <SCRIPT SRC="./JS/particulas.js"></SCRIPT>
        <SCRIPT SRC="./JS/deriva.js"></SCRIPT>
        <SCRIPT SRC="./JS/abrirPub.js"></SCRIPT>
        <script src="./JS/crearpub.js"></script>
        <SCRIPT SRC="./JS/stories.js"></SCRIPT>
        <SCRIPT SRC="./JS/contador.js"></SCRIPT>
        <SCRIPT SRC="./JS/solicitudes.js"></SCRIPT>
        <SCRIPT SRC="./JS/funcionalidades.js"></SCRIPT>
          <script src="./JS/tema.js"></script>
    </BODY>

    <FOOTER>
    </FOOTER>

</HTML>
