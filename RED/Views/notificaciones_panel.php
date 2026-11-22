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

            <!-- Se llena desde JS/notificaciones.js con NotificacionController.php -->
            <div class="notif-lista" id="notifLista"></div>

        </DIV>