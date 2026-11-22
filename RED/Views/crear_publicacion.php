<?php
$svgAvatar = '<svg xmlns="http://www.w3.org/2000/svg" width="90" height="90"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8e5cdc"/><stop offset="1" stop-color="#3cc8e6"/></linearGradient></defs><rect width="90" height="90" fill="url(#g)"/><text x="45" y="58" font-family="Arial,sans-serif" font-size="34" font-weight="700" fill="#ffffff" text-anchor="middle">' . htmlspecialchars($inicialesHeader, ENT_QUOTES, "UTF-8") . '</text></svg>';
$avatarPorDefecto = "data:image/svg+xml;utf8," . rawurlencode($svgAvatar);
?>
<DIV CLASS="crear-publi">

            <div class="crear-publi-header">

                <div>

                    <h2>
                        Crear publicación
                    </h2>

                </div>

                <button class="cerrar-publi" type="button">

                    <i class="ion-md-close"></i>

                </button>

            </div>

            <div class="crear-publi-usuario">

                <img src="../Controllers/ImagenController.php?tipo=foto&v=<?= $v ?>"
                     alt="foto usuario"
                     onerror="this.onerror=null; this.src='<?= $avatarPorDefecto ?>'">

                <div>

                    <strong>
                        @<?= h($tagHeader) ?>
                    </strong>

                    <span>
                        Publicación pública
                    </span>

                </div>

            </div>

            <form action="../Controllers/CrearPost.php" >

                <textarea
                    placeholder="¿Qué está pasando por tu mente?"
                    maxlength="500"></textarea>

                <div class="crear-publi-footer">

                    <div class="crear-publi-opciones">

                        <button type="button"
                                title="Agregar foto">

                            <i class="ion-md-image"></i>

                        </button>

                        <button type="button"
                                title="Agregar video">

                            <i class="ion-md-videocam"></i>

                        </button>

                        <button type="button"
                                title="Agregar emoji">

                            <i class="ion-md-happy"></i>

                        </button>

                    </div>

                    <button type="submit"
                            class="btn-publicar">

                        Publicar

                    </button>

                </div>

            </form>

        </DIV>