<DIV CLASS="crear-publi editar-publi" id="editarPubli">

            <div class="crear-publi-header">

                <div>
                    <h2>Editar publicación</h2>
                </div>

                <button class="cerrar-publi" type="button" id="editarCerrar">
                    <i class="ion-md-close"></i>
                </button>

            </div>

            <form id="form-editar-publi" enctype="multipart/form-data">

                <textarea name="texto"
                    placeholder="¿Qué está pasando por tu mente?"
                    maxlength="255"></textarea>

                <input type="file"
                       name="archivo"
                       id="editar-archivo"
                       accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg"
                       hidden>

                <div class="crear-publi-preview" id="editar-preview" style="display:none;">
                    <img alt="vista previa">
                    <video controls></video>
                    <button type="button" id="editar-quitar" title="Quitar foto o video">
                        <i class="ion-md-close"></i>
                    </button>
                </div>

                <p class="crear-publi-mensaje" id="editar-mensaje"></p>

                <div class="crear-publi-footer">

                    <div class="crear-publi-opciones">

                        <button type="button" title="Cambiar por una foto" id="editar-btn-foto">
                            <i class="ion-md-image"></i>
                        </button>

                        <button type="button" title="Cambiar por un video" id="editar-btn-video">
                            <i class="ion-md-videocam"></i>
                        </button>

                    </div>

                    <button type="submit" class="btn-publicar">
                        Guardar
                    </button>

                </div>

            </form>

        </DIV>

        <DIV CLASS="confirmar-eliminar" id="confirmarEliminar">

            <div class="confirmar-caja">

                <h3>¿Eliminar publicación?</h3>

                <p>También se borrarán sus comentarios y reacciones. Esto no se puede deshacer.</p>

                <p class="crear-publi-mensaje" id="eliminar-mensaje"></p>

                <div class="confirmar-botones">
                    <button type="button" class="confirmar-cancelar" id="cancelarEliminar">Cancelar</button>
                    <button type="button" class="confirmar-aceptar" id="aceptarEliminar">Eliminar</button>
                </div>

            </div>

        </DIV>