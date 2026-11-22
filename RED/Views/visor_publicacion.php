<DIV CLASS="visor" id="visor" aria-hidden="true">

            <button type="button" class="visor-cerrar" id="visorCerrar" title="Cerrar">
                <i class="ion-md-close"></i>
            </button>

            <div class="visor-imagen">
                <img id="visorImg" alt="imagen de la publicación">
            </div>

            <aside class="visor-panel" id="visorPanel" data-id-pub="">

                <div class="userinfo">

                    <img id="visorAutorFoto" class="foto" alt="foto">

                    <div class="username">
                        <p id="visorNombre"></p>
                        <p id="visorTiempo"></p>
                    </div>

                </div>

                <div class="post-text" id="visorTexto"></div>

                <div class="post-actions">

                    <span>
                        <button type="button" class="iconos like_btn" id="visorLike">
                            <i class="ion-md-heart-empty like int"></i>
                        </button>
                        <span class="contador-likes" id="visorContLikes">0</span>
                    </span>

                    <span id="visorBtnComentar">
                        <button type="button" class="iconos"><i class="ion-md-chatbubbles int"></i></button>
                        <span class="contador-comentarios" id="visorContComentarios">0</span>
                    </span>

                </div>

                <div class="lista-comentarios" id="visorComentarios"></div>

                <form class="form-comentario visor-form">
                    <input type="text"
                           name="texto"
                           maxlength="255"
                           autocomplete="off"
                           placeholder="Escribe tu comentario"
                           class="inputs">
                </form>

            </aside>

        </DIV>