<?php
    session_start();//para recordar quién inició sesión

    //las publicaciones se pueden ver sin cuenta; para comentar y dar like hay que iniciar sesion
    $paginaPublica = true;
    require_once 'usuario_header.php';
    require_once '../Controllers/IndividualPostController.php';

    $idSesion = $hayUsuario ? (int) $_SESSION['id_usuario'] : 0;
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
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/notificaciones.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/posts.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/fondo.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/botella.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/crearpub.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/funcionalidades.css">
        <link rel="stylesheet" TYPE="TEXT/CSS" href="./CSS/modo-claro.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/solicitudes.css">
        <link rel="stylesheet" TYPE="TEXT/CSS" href="./CSS/visor.css">

         <script>
            const temaInicial = localStorage.getItem("cosmix-tema") || "dark";
            document.documentElement.classList.add(
                temaInicial === "light" ? "modo-claro" : "modo-oscuro"
            );
        </script>
    </HEAD>

    <BODY data-sesion="<?= $hayUsuario ? '1' : '0' ?>">

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

                <li onclick="window.location.href='Solicitudes.html'">
                    <i class="ion-md-people"></i> Solicitudes de amistad
                </li>

            </ul>

            <button class="create-post-btn">Publicar</button>

        </DIV>

        <?php if ($hayUsuario): ?>
            <?php require __DIR__ . "/menu_usuario.php"; ?>
        <?php endif; ?>

        <?php if ($hayUsuario): ?>
            <?php require __DIR__ . "/notificaciones_panel.php"; ?>
        <?php endif; ?>

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

                <?php if ($hayUsuario): ?>

                    <i class="ion-md-notifications-outline" id="notifTrigger"></i>

                    <i class="ion-md-mail-outline"></i>

                    <div class="profile"
                         id="profileTrigger" style="overflow:hidden;"
                         onclick="document.getElementById('profileMenu').classList.toggle('active')">
                        <?= avatarUsuario("headerAvatar","headerIniciales",$inicialesHeader,$v) ?>
                    </div>

                <?php else: ?>

                    <div class="botones-sesion">
                        <a href="login.php" class="btn-sesion">Iniciar sesión</a>
                        <a href="register.php" class="btn-sesion btn-sesion-crear">Crear cuenta</a>
                    </div>

                <?php endif; ?>

            </div>

        </DIV>

       

        <?php if ($hayUsuario): ?>
            <?php require __DIR__ . "/crear_publicacion.php"; ?>
        <?php endif; ?>

        <DIV CLASS="body">

            <DIV CLASS="ctr">

             
                <!-- Apartado filtrar publis funcionalidad 2 -->

                
                
               
                    <article CLASS="post" data-id-pub="<?= (int)$publicaciones['id_pub'] ?>">

                        <DIV CLASS="userinfo">

                            <img src="<?= $publicaciones['foto_src']?>"
                                alt="no cargo"
                                class="foto">

                            <DIV class="username">

                                <p>
                                    <?= htmlspecialchars($publicaciones['nombre'])?>
                                </p>

                                <p>
                                    <a href="verPublicacion.php?id=<?= (int)$publicaciones['id_pub'] ?>"
                                    class="post-enlace"
                                    title="<?= htmlspecialchars($publicaciones['fecha_pub']) ?>">
                                        <?= htmlspecialchars($publicaciones['hace']) ?>
                                    </a>
                                </p>

                            </DIV>

                            <?php if ($hayUsuario && (int) $publicaciones['id_usuario'] === $idSesion): ?>
                                <div class="post-menu">
                                    <button type="button" class="post-menu-btn" title="Opciones">
                                        <i class="ion-md-more"></i>
                                    </button>
                                    <div class="post-menu-lista">
                                        <button type="button" class="post-editar">
                                            <i class="ion-md-create"></i> Editar
                                        </button>
                                        <button type="button" class="post-eliminar">
                                            <i class="ion-md-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </DIV>

                         <?php if ($publicaciones['texto'] != null):?>
                            <div class="post-text">
                                <?= htmlspecialchars($publicaciones['texto'])?>
                            </div>
                        <?php endif; ?> 

                        <?php if ($publicaciones['media_src'] !== ''): ?>
                            <?php if ($publicaciones['es_video']): ?>
                                <video src="<?= $publicaciones['media_src'] ?>" class="post-video" controls preload="metadata"></video>
                            <?php else: ?>
                                <img src="<?= $publicaciones['media_src'] ?>" alt="imagen de la publicación" class="post-img" loading="lazy">
                            <?php endif; ?>
                        <?php endif; ?>
                    
                        <DIV CLASS="post-actions">

                            <span>
                                <button type="button"
                                        class="iconos like_btn"
                                        data-liked="<?= !empty($publicaciones['dio_like']) ? '1' : '0' ?>">
                                    <i class="ion-md-heart-empty like int"></i>
                                </button>
                                <span class="contador-likes"><?= (int) $publicaciones['likes'] ?></span>
                            </span>

                            <span onclick="this.closest('.post').querySelector('.form-comentario input').focus()">
                                <button type="button" class="iconos comment_btn"><i class="ion-md-chatbubbles comment int"></i></button>
                                <span class="contador-comentarios"><?= (int) $publicaciones['comentarios'] ?></span>
                            </span>

                            <span>
                                <button type="button" class="iconos guardar_btn"><i class="ion-md-bookmark save int"></i></button>
                                <?= (int) $publicaciones['guardados'] ?>
                            </span>

                        </DIV>

                        <DIV CLASS="lista-comentarios" data-cargar="1"></DIV>

                        <FORM class="form-comentario">
                            <INPUT TYPE="text"
                                   name="texto"
                                   maxlength="255"
                                   autocomplete="off"
                                   PLACEHOLDER="Escribe tu comentario"
                                   CLASS="inputs">
                        </FORM>
                        
                </article>
                
           
            
            </DIV>
            <DIV CLASS="right">

                <?php if ($hayUsuario): ?>
                <DIV CLASS="panel">

                    <div class="panel-title">
                        Sugerencias para ti
                    </div>

                    <div id="sugerenciasLista"></div>

                    <p class="solicitudes-msg" id="solicitudesMensaje"></p>

                    <a class="ver-mas-sugerencias" href="sugerencias.php">Ver más sugerencias</a>

                </DIV>
                <?php endif; ?>

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

                    <img src="<?= $fotoUsuarioSrc ?>"
                         alt="foto usuario"
                         onerror="this.onerror=null; this.src='<?= $fotoPorDefecto ?>'">

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

                <!-- Filtros -->

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
        <?php require __DIR__ . "/visor_publicacion.php"; ?>
        <?php if ($hayUsuario): ?>
            <?php require __DIR__ . "/editar_publicacion.php"; ?>
        <?php else: ?>
            <?php require __DIR__ . "/aviso_sesion.php"; ?>
        <?php endif; ?>

        <SCRIPT SRC="./JS/buttons.js"></SCRIPT>
        <script src="./JS/notificaciones.js"></script>
        <SCRIPT SRC="./JS/particulas.js"></SCRIPT>
        <SCRIPT SRC="./JS/deriva.js"></SCRIPT>
        <SCRIPT SRC="./JS/abrirPub.js"></SCRIPT>
        <script src="./JS/media.js"></script>
        <script src="./JS/crearpub.js"></script>
        <SCRIPT SRC="./JS/stories.js"></SCRIPT>
        <SCRIPT SRC="./JS/contador.js"></SCRIPT>
        <SCRIPT SRC="./JS/solicitudes.js"></SCRIPT>
        <SCRIPT SRC="./JS/funcionalidades.js"></SCRIPT>
        <script src="./JS/tema.js"></script>
         <SCRIPT SRC="./JS/interaccion.js"></SCRIPT>
        <script src="./JS/visor.js"></script>
        <script src="./JS/editarpub.js"></script>
    </BODY>

    <FOOTER>
    </FOOTER>

</HTML>