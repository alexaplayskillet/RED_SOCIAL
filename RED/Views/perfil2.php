<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../model/conexion.php";
require_once __DIR__ . "/../model/UsuarioModelo.php";
require_once __DIR__ . "/../Controllers/ShowOwnPostsController.php";
require_once __DIR__ . "/../Controllers/EstLikesController.php";
require_once __DIR__ . "/../Controllers/EstCommsController.php";
require_once __DIR__ . "/../Controllers/EstPubsController.php";
require_once __DIR__ . "/../Controllers/EstAmigosController.php";

$modelo = new UsuarioModelo($conexion);
$perfil = $modelo->obtenerPerfil((int) $_SESSION["id_usuario"]);

if (!$perfil) {
    session_destroy();
    header("Location: login.php");
    exit;
}

function h($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

require_once __DIR__ . "/usuario_header.php";
$v = time();
$tag = $perfil["tag"];
$nombre = trim($perfil["nombre"]) !== "" ? $perfil["nombre"] : $tag;

$partes = preg_split("/\s+/", trim($nombre));
if (count($partes) > 1) {
    $iniciales = mb_strtoupper(mb_substr($partes[0], 0, 1) . mb_substr($partes[1], 0, 1));
} else {
    $iniciales = mb_strtoupper(mb_substr($nombre, 0, 2));
}

$meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio",
          "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
$fecha = new DateTime($perfil["fecha_nac"]);
$fechaNac = $fecha->format("j") . " de " . $meses[(int) $fecha->format("n") - 1] . " de " . $fecha->format("Y");
?>
<!DOCTYPE HTML>
<HTML>
    <HEAD>
        <meta charset="UTF-8">
        <title>Mi Perfil</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <link href="./LIBS/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/distribucion.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/movil.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/menus.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/notificaciones.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/posts.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/fondo.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/crearpub.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/perfil.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/editarPerfil.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/amigos.css">
        <LINK REL="STYLESHEET" TYPE="TEXT/CSS" HREF="./CSS/galeria.css">
<link rel="stylesheet" href="./CSS/modo-claro.css">
        <link rel="stylesheet" TYPE="TEXT/CSS" href="./CSS/visor.css">

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
                    <img id="headerAvatar" class="profile-foto" alt="Foto de perfil"
                         src="../Controllers/ImagenController.php?tipo=foto&v=<?= $v ?>"
                         style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;"
                         onload="this.style.display='block'; document.getElementById('headerIniciales').style.display='none'"
                         onerror="this.style.display='none'">
                    <span id="headerIniciales"><?= h($iniciales) ?></span>
                </div>
            </div>
        </DIV>

        <DIV CLASS="body profile-page">

            <div class="profile-cover-section">
                <button type="button" class="profile-cover-delete" id="btnEliminarPortada" title="Eliminar portada">
                    <i class="ion-md-trash"></i>
                </button>
                <label for="coverUpload" class="profile-cover" id="coverLabel">
                    <img id="coverPreview" class="profile-cover-img" alt="Portada"
                         src="../Controllers/ImagenController.php?tipo=portada&v=<?= $v ?>"
                         style="display:none;"
                         onload="this.style.display='block'"
                         onerror="this.style.display='none'">
                    <div class="profile-cover-edit"><i class="ion-md-camera"></i> Editar portada</div>
                </label>
                <input type="file" id="coverUpload" accept="image/jpeg,image/png,image/webp" style="display:none;">

                <div class="profile-header">
                    <label for="avatarUpload" class="profile-avatar-lg" id="avatarLabel">
                        <div class="profile-avatar-inner">
                            <img id="avatarPreview" alt="Foto de perfil"
                                 src="../Controllers/ImagenController.php?tipo=foto&v=<?= $v ?>"
                                 style="display:none;"
                                 onload="this.style.display='block'; document.getElementById('avatarInitials').style.display='none'"
                                 onerror="this.style.display='none'">
                            <span id="avatarInitials"><?= h($iniciales) ?></span>
                        </div>
                        <div class="profile-avatar-edit"><i class="ion-md-create"></i></div>
                    </label>
                    <input type="file" id="avatarUpload" accept="image/jpeg,image/png,image/webp" style="display:none;">

                    <div class="profile-name-block">
                        <h1><?= h($nombre) ?></h1>
                        <p>@<?= h($tag) ?></p>
                    </div>
                    <button type="button" class="btn profile-edit-btn" id="btnEditarPerfil"><i class="ion-md-create"></i> Editar perfil</button>
                </div>

                <div class="profile-tabs">
                    <div class="profile-tab active" data-tab="informacion"><i class="ion-md-information-circle"></i> Información</div>
                    <div class="profile-tab" data-tab="amigos"><i class="ion-md-people"></i> Amigos</div>
                    <div class="profile-tab" data-tab="galeria"><i class="ion-md-images"></i> Galería</div>
                    <div class="profile-tab" data-tab="estadisticas"><i class="ion-md-stats"></i> Estadísticas</div>
                    <div class="profile-tab" data-tab="configuracion"><i class="ion-md-settings"></i> Configuración</div>
                </div>
            </div>

            <div class="tab-panel active" id="tab-informacion">
                <div class="profile-content">

                    <div class="profile-left">
                        <div class="panel">
                            <div class="panel-title">Datos personales</div>
                            <div class="dato-item"><i class="ion-md-mail"></i> <?= h($perfil["correo"]) ?></div>
                            <div class="dato-item"><i class="ion-md-calendar"></i> <?= h($fechaNac) ?></div>
                        </div>

                        <div class="panel">
                            <div class="panel-title">Amigos</div>
                            <div class="amigos-grid" id="amigosRecientes"></div>
                            <p class="amigos-vacio" id="amigosRecientesVacio">Aún no tienes amigos agregados.</p>
                            <a class="ver-todos" data-tab-link="amigos">Ver todos los amigos</a>
                        </div>

                        <div class="panel">
                            <div class="panel-title">Galería</div>
                            <div class="galeria-grid" id="galeriaRecientes"></div>
                            <p class="galeria-vacio" id="galeriaRecientesVacio">Aún no has publicado fotos.</p>
                            <a class="ver-todos" data-tab-link="galeria">Ver toda la galería</a>
                        </div>
                    </div>

                    <div class="profile-right">
                        <div class="panel crear-post">
                            <img src="<?= $fotoUsuarioSrc ?>" alt="foto usuario" class="foto" onerror="this.onerror=null; this.src='<?= $fotoPorDefecto ?>'">
                            <input type="text" class="inputs crear-post-input" placeholder="¿Qué está pasando por tu mente?">
                            <div class="crear-post-icons">
                                <button class="icon-btn" title="Agregar foto"><i class="ion-md-image"></i></button>
                                <button class="icon-btn" title="Agregar video"><i class="ion-md-videocam"></i></button>
                            </div>
                        </div>

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
                                    class="post-enlace"
                                    title="<?= htmlspecialchars($publicacion['fecha_pub']) ?>">
                                        <?= htmlspecialchars($publicacion['hace']) ?>
                                    </a>
                                </p>

                            </DIV>

                            <?php if ((int) $publicacion['id_usuario'] === (int) $_SESSION['id_usuario']): ?>
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

                         <?php if ($publicacion['texto'] != null):?>
                            <div class="post-text">
                                <?= htmlspecialchars($publicacion['texto'])?>
                            </div>
                        <?php endif; ?> 

                        <?php if ($publicacion['media_src'] !== ''): ?>
                            <?php if ($publicacion['es_video']): ?>
                                <video src="<?= $publicacion['media_src'] ?>" class="post-video" controls preload="metadata"></video>
                            <?php else: ?>
                                <img src="<?= $publicacion['media_src'] ?>" alt="imagen de la publicación" class="post-img" loading="lazy">
                            <?php endif; ?>
                        <?php endif; ?>
                    
                        <DIV CLASS="post-actions">

                            <span>
                                <button type="button"
                                        class="iconos like_btn"
                                        data-liked="<?= !empty($publicacion['dio_like']) ? '1' : '0' ?>">
                                    <i class="ion-md-heart-empty like int"></i>
                                </button>
                                <span class="contador-likes"><?= (int) $publicacion['likes'] ?></span>
                            </span>

                            <span onclick="this.closest('.post').querySelector('.caja_com').classList.toggle('active')">
                                <button type="button" class="iconos comment_btn"><i class="ion-md-chatbubbles comment int"></i></button>
                                <span class="contador-comentarios"><?= (int) $publicacion['comentarios'] ?></span>
                            </span>

                            <span>
                                <button type="button" class="iconos guardar_btn"><i class="ion-md-bookmark save int"></i></button>
                                <?= (int) $publicacion['guardados'] ?>
                            </span>

                        </DIV>

                        <DIV CLASS="caja_com">

                            <FORM class="form-comentario">
                                <INPUT TYPE="text"
                                       name="texto"
                                       maxlength="255"
                                       autocomplete="off"
                                       PLACEHOLDER="Escribe tu comentario"
                                       CLASS="inputs">
                            </FORM>

                            <a href="verPublicacion.php?id=<?= (int) $publicacion['id_pub'] ?>" class="ver-comentarios">
                                Ver comentarios
                            </a>

                        </DIV>
                        
                </article>
                    <?php endforeach; ?>
                
           

                    </div>

                </div>
            </div>

            <div class="tab-panel" id="tab-amigos">
                <div class="panel amigos-panel-full">
                    <div class="amigos-header">
                        <h2>Amigos</h2>
                        <p class="amigos-subtitulo" id="amigosSubtitulo">0 amigos</p>
                        <div class="amigos-search">
                            <i class="ion-md-search"></i>
                            <input type="text" id="amigosBuscar" placeholder="Buscar amigos" autocomplete="off">
                        </div>
                    </div>

                    <div class="amigos-grid-full" id="amigosGrid"></div>
                    <p class="amigos-vacio" id="amigosVacio"></p>
                </div>
            </div>

            <div class="tab-panel" id="tab-galeria">
                <div class="panel fotos-panel-full">
                    <div class="fotos-header">
                        <h2>Tus fotos</h2>
                        <div class="fotos-header-actions">
                            <a class="link-accion" id="btnAgregarFotos"><i class="ion-md-add"></i> Agregar fotos/video</a>
                        </div>
                    </div>

                    <div class="fotos-grid-full" id="galeriaGrid"></div>
                    <p class="galeria-vacio" id="galeriaVacio">Aún no has publicado fotos.</p>
                </div>
            </div>

            <div class="tab-panel" id="tab-estadisticas">
                <div class="panel estadisticas-panel-full">
                    <div class="estadisticas-header">
                        <h2>Estadísticas</h2>
                        <p class="estadisticas-subtitulo">Tu actividad y crecimiento en Cosmix</p>
                    </div>

                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-card-icon"><i class="ion-md-heart"></i></div>
                            <div class="stat-card-valor" id="statLikes"><?= (int)$likes ?></div>
                            <div class="stat-card-label">Me gusta recibidos</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-icon"><i class="ion-md-chatbubbles"></i></div>
                            <div class="stat-card-valor" id="statComentarios"><?= (int)$comentarios ?></div>
                            <div class="stat-card-label">Comentarios recibidos</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-icon"><i class="ion-md-people"></i></div>
                            <div class="stat-card-valor" id="statAmigosMes"><?= (int)$cantAmi ?></div>
                            <div class="stat-card-label">Amigos Totales</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-icon"><i class="ion-md-create"></i></div>
                            <div class="stat-card-valor" id="statPostsMes"><?= (int)$publis ?></div>
                            <div class="stat-card-label">Publicaciones este mes</div>
                        </div>
                    </div>

                    <div class="reporte-selector">
                        <div class="reporte-selector-texto">
                            <span class="reporte-selector-titulo">Generar reporte de estadísticas</span>
                            <span class="reporte-selector-desc">Elige qué quieres consultar y genera tu reporte</span>
                        </div>

                        <div class="reporte-controles">
                            <select class="reporte-select" id="tipoReporte">
                                <option value="general">Resumen general</option>
                                <option value="amigos">Amigos agregados (mes)</option>
                                <option value="publicaciones">Publicaciones creadas (mes)</option>
                            </select>
                            <button class="btn btn-generar-reporte" id="btnGenerarReporte">
                                <i class="ion-md-stats"></i> Generar reporte
                            </button>
                        </div>
                    </div>

                    <div class="reporte-placeholder" id="reportePlaceholder">
                        <i class="ion-md-bar-chart"></i>
                        <p>Selecciona un tipo de reporte y presiona "Generar reporte" para ver los resultados.</p>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-configuracion">
                <div class="panel configuracion-panel-full">

                    <div class="configuracion-header">
                        <h2>Configuración</h2>
                        <p class="configuracion-subtitulo">Activa o desactiva funciones de tu experiencia en Cosmix</p>
                    </div>

                    <div class="config-item">
                        <div class="config-item-icono"><i class="ion-md-water"></i></div>
                        <div class="config-item-texto">
                            <span class="config-item-titulo">Mensaje a la deriva</span>
                            <span class="config-item-desc">Recibe y envía mensajes anónimos flotando en una botella junto a otros usuarios de Cosmix.</span>
                        </div>
                        <label class="config-switch">
                            <input type="checkbox" id="toggleDeriva" checked>
                            <span class="config-switch-slider"></span>
                        </label>
                    </div>

                    <div class="config-item">
                        <div class="config-item-icono"><i class="ion-md-time"></i></div>
                        <div class="config-item-texto">
                            <span class="config-item-titulo">Contador de tiempo</span>
                            <span class="config-item-desc">Muestra en pantalla cuánto tiempo llevas navegando en la página durante tu sesión actual.</span>
                        </div>
                        <label class="config-switch">
                            <input type="checkbox" id="toggleContador" checked>
                            <span class="config-switch-slider"></span>
                        </label>
                    </div>

                    <div class="config-item">
                        <div class="config-item-icono"><i class="ion-md-notifications"></i></div>
                        <div class="config-item-texto">
                            <span class="config-item-titulo">Notificaciones</span>
                            <span class="config-item-desc">Recibe avisos sobre likes, comentarios, solicitudes de amistad y otras novedades.</span>
                        </div>
                        <label class="config-switch">
                            <input type="checkbox" id="toggleNotificaciones" checked>
                            <span class="config-switch-slider"></span>
                        </label>
                    </div>

                </div>
            </div>

        </DIV>
        <div class="editar-overlay" id="editarPerfilOverlay">
            <div class="editar-modal" role="dialog" aria-modal="true" aria-labelledby="editarPerfilTitulo">
                <button type="button" class="editar-cerrar" id="cerrarEditarPerfil" aria-label="Cerrar">
                    <i class="ion-md-close"></i>
                </button>
                <h2 id="editarPerfilTitulo">Editar perfil</h2>
                <p class="editar-sub">Actualiza tus datos personales.</p>

                <form id="formEditarPerfil">
                    <label for="editNombre">Nombre completo</label>
                    <input type="text" id="editNombre" name="nombre" maxlength="100"
                           value="<?= h($perfil["nombre"]) ?>" required>

                    <label for="editTag">Usuario</label>
                    <input type="text" id="editTag" name="tag" maxlength="16" pattern="[A-Za-z0-9_\-]{4,16}"
                           value="<?= h($tag) ?>" required>
                    <small>Es el que usas para iniciar sesión. De 4 a 16 caracteres.</small>

                    <label for="editCorreo">Correo</label>
                    <input type="email" id="editCorreo" name="correo" maxlength="100"
                           value="<?= h($perfil["correo"]) ?>" required>

                    <label for="editFecha">Fecha de nacimiento</label>
                    <input type="date" id="editFecha" name="fecha"
                           value="<?= h($perfil["fecha_nac"]) ?>" required>

                    <p class="editar-mensaje" id="editarPerfilMensaje"></p>

                    <div class="editar-acciones">
                        <button type="button" class="btn btn-secundario" id="cancelarEditarPerfil">Cancelar</button>
                        <button type="submit" class="btn">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

                <div class="editar-overlay" id="confirmarAmigoOverlay">
            <div class="editar-modal">
                <button type="button" class="editar-cerrar" id="confirmarAmigoCerrar"><i class="ion-md-close"></i></button>
                <h2>Eliminar amigo</h2>
                <p class="editar-sub">Esta acción quitará la amistad de ambos perfiles.</p>
                <p class="confirmar-texto" id="confirmarAmigoTexto"></p>
                <p class="editar-mensaje" id="confirmarAmigoMensaje"></p>
                <div class="editar-acciones">
                    <button type="button" class="btn btn-secundario" id="confirmarAmigoCancelar">Cancelar</button>
                    <button type="button" class="btn btn-peligro" id="confirmarAmigoAceptar">Eliminar</button>
                </div>
            </div>
        </div>

        <div class="editar-overlay" id="editarFotoOverlay">
            <div class="editar-modal">
                <button type="button" class="editar-cerrar" id="editarFotoCerrar"><i class="ion-md-close"></i></button>
                <h2>Editar foto</h2>
                <p class="editar-sub">Cambia el texto que acompaña a tu foto.</p>
                <label for="editarFotoTexto">Texto</label>
                <textarea id="editarFotoTexto" maxlength="255" placeholder="Escribe algo sobre esta foto"></textarea>
                <p class="editar-mensaje" id="editarFotoMensaje"></p>
                <div class="editar-acciones">
                    <button type="button" class="btn btn-secundario" id="editarFotoCancelar">Cancelar</button>
                    <button type="button" class="btn" id="editarFotoGuardar">Guardar</button>
                </div>
            </div>
        </div>

        <div class="editar-overlay" id="eliminarFotoOverlay">
            <div class="editar-modal">
                <button type="button" class="editar-cerrar" id="eliminarFotoCerrar"><i class="ion-md-close"></i></button>
                <h2>Eliminar foto</h2>
                <p class="editar-sub">Esta acción no se puede deshacer.</p>
                <p class="confirmar-texto">¿Quieres eliminar esta foto? También se borrarán sus likes y comentarios.</p>
                <p class="editar-mensaje" id="eliminarFotoMensaje"></p>
                <div class="editar-acciones">
                    <button type="button" class="btn btn-secundario" id="eliminarFotoCancelar">Cancelar</button>
                    <button type="button" class="btn btn-peligro" id="eliminarFotoAceptar">Eliminar</button>
                </div>
            </div>
        </div>

        <?php require __DIR__ . "/crear_publicacion.php"; ?>

        <div class="visor-foto" id="visorFoto">
            <button type="button" class="visor-foto-cerrar" id="visorFotoCerrar"><i class="ion-md-close"></i></button>
            <img id="visorFotoImg" alt="Foto">
            <p class="visor-foto-texto" id="visorFotoTexto"></p>
        </div>

        <script src="./LIBS/jquery-4.0.0.min.js"></script>
        <script src="./LIBS/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <?php require __DIR__ . "/visor_publicacion.php"; ?>
        <?php require __DIR__ . "/editar_publicacion.php"; ?>

        <SCRIPT SRC="./JS/buttons.js"></SCRIPT>
        <script src="./JS/notificaciones.js"></script>
        <SCRIPT SRC="./JS/editar_perfil.js"></SCRIPT>
        <SCRIPT SRC="./JS/amigos.js"></SCRIPT>
        <SCRIPT SRC="./JS/galeria.js"></SCRIPT>
        <SCRIPT SRC="./JS/abrirPublicacion.js"></SCRIPT>
        <SCRIPT SRC="./JS/particulas.js"></SCRIPT>
        <SCRIPT SRC="./JS/configuracion.js"></SCRIPT>
        <SCRIPT SRC="./JS/abrirPub.js"></SCRIPT>
        <script src="./JS/tema.js"></script>
        <script src="./JS/media.js"></script>
        <script src="./JS/crearpub.js"></script>
        <SCRIPT SRC="./JS/interaccion.js"></SCRIPT>
        <script src="./JS/visor.js"></script>
        <script src="./JS/editarpub.js"></script>
    </BODY>
    <FOOTER>
        
    </FOOTER>
</HTML>